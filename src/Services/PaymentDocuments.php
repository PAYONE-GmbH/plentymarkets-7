<?php

namespace Payone\Services;

use Carbon\Carbon;
use Payone\Adapter\Logger;
use Payone\Helpers\PaymentHelper;
use Payone\Methods\PayoneInvoiceSecurePaymentMethod;
use Plenty\Modules\Authorization\Services\AuthHelper;
use Plenty\Modules\Document\Models\Document;
use Payone\Providers\Api\Request\GetInvoiceDataProvider;
use Plenty\Modules\Document\Contracts\DocumentRepositoryContract;
use Plenty\Modules\Order\Contracts\OrderRepositoryContract;
use Plenty\Modules\Order\Models\Order;
use Plenty\Modules\Order\Models\OrderType;
use Plenty\Modules\Otto\Order\Exceptions\InvalidDocumentTypeException;
use Plenty\Modules\Payment\Contracts\PaymentRepositoryContract;
use Plenty\Modules\Payment\Models\Payment;
use Plenty\Modules\Payment\Models\PaymentProperty;
use Plenty\Repositories\Models\PaginatedResult;

/**
 * Class PaymentDocuments
 */
class PaymentDocuments
{
    /**
     * @var PaymentRepositoryContract
     */
    private $paymentRepository;

    /**
     * @var PaymentHelper
     */
    private $paymentHelper;

    /**
     * @var Api
     */
    private $api;
    /**
     * @var GetInvoiceDataProvider
     */
    private $getInvoiceDataProvider;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * PaymentCreation constructor.
     *
     * @param PaymentRepositoryContract $paymentRepository
     * @param PaymentHelper $paymentHelper
     * @param GetInvoiceDataProvider $getInvoiceDataProvider
     * @param Api $api
     * @param Logger $logger
     */
    public function __construct(
        PaymentRepositoryContract $paymentRepository,
        PaymentHelper $paymentHelper,
        GetInvoiceDataProvider $getInvoiceDataProvider,
        Api $api,
        Logger $logger
    )
    {
        $this->paymentRepository = $paymentRepository;
        $this->paymentHelper = $paymentHelper;
        $this->api = $api;
        $this->getInvoiceDataProvider = $getInvoiceDataProvider;
        $this->logger = $logger;
    }

    public function uploadDocument($txid, $sequenceNumber, $invoiceId, $invoiceDate)
    {
        $payments = $this->paymentRepository->getPaymentsByPropertyTypeAndValue(
            PaymentProperty::TYPE_TRANSACTION_ID,
            $txid,
            1
        );
        /* @var $payment Payment */
        foreach ($payments as $payment) {
            $this->logger->info('Api.doGetInvoice',
                [
                    'taxid' => $txid,
                    'sequenceNumber' => $sequenceNumber,
                    'payment' => $payment,
                ]
            );

            //only for secure invoice
            if ($this->paymentHelper->getMopId(PayoneInvoiceSecurePaymentMethod::PAYMENT_CODE) !=
                $payment->mopId) {
                continue;
            }

            $orderId = $payment->order->orderId;
            if ((int)$orderId <= 0) {
                continue;
            }

            try {
                $authHelper = pluginApp(AuthHelper::class);
                $order      = $authHelper->processUnguarded(
                    function () use ($orderId) {
                        $orderRepository = pluginApp(OrderRepositoryContract::class);
                        return $orderRepository->findOrderById($orderId);
                    }
                );
            } catch (\Exception $ex) {
                $this->logger->error('Api.doGetInvoice',
                    [
                        'taxid' => $txid,
                        'sequenceNumber' => $sequenceNumber,
                        'payment' => $payment,
                        'errorMessage' => $ex->getMessage(),
                    ]
                );
                continue;
            }

            if(strlen($invoiceId) === 0)
            {
                $invoiceId =  (($order->typeId == OrderType::TYPE_CREDIT_NOTE)?'GT':'RG').'-'.$txid.'-'.$sequenceNumber;
            }

            $paymentCode = $this->paymentHelper->getPaymentCodeByMop($payment->mopId);
            $requestData = $this->getInvoiceDataProvider->getRequestData($paymentCode, $txid, $sequenceNumber, $invoiceId);
            $getInvoiceResult = $this->api->doGetInvoice($requestData);

            if (!$getInvoiceResult->getSuccess()) {
                $this->logger->error('Api.doGetInvoice',
                    [
                        'taxid' => $txid,
                        'sequenceNumber' => $sequenceNumber,
                        'payment' => $payment,
                        'errorMessage' => $getInvoiceResult->getErrorMessage(),
                    ]
                );
                continue;
            }

            $this->importInvoice($order, $requestData['context']['documentNumber'], $getInvoiceResult->getBase64(), $invoiceDate);
        }
    }

    /**
     * Imports one invoice.
     *
     * @param Order $order
     * @param array $invoice
     * @throws InvalidDocumentTypeException
     * @throws \Plenty\Exceptions\ValidationException
     */
    private function importInvoice(Order $order, string $invoiceNumber, string $content, string $invoiceDate)
    {
        /** @var DocumentRepositoryContract $documentRepository */
        $documentRepository = pluginApp(DocumentRepositoryContract::class);

        // check if the document is already imported
        $documentRepository->setFilters([
            'numberWithPrefix' => $invoiceNumber,
            'orderId' => $order->id
        ]);

        /** @var PaginatedResult $result */
        $result = $documentRepository->find();

        if ($result->getTotalCount() > 0) {
            return;
        }
        $documentType = Document::INVOICE_EXTERNAL;
        if ($order->typeId == OrderType::TYPE_CREDIT_NOTE) {
            $documentType = Document::CREDIT_NOTE_EXTERNAL;
        }

        $date = Carbon::now()->toW3cString();
        if( strlen($invoiceDate) == 8 )
        {
            $date = Carbon::createFromFormat('Ymd', $invoiceDate)->format(\DateTime::W3C);
        }

        $data = [
            'documents' => [
                [
                    'content' => $content,
                    'numberWithPrefix' => $invoiceNumber,
                    'displayDate' => $date
                ]
            ]
        ];
        $authHelper = pluginApp(AuthHelper::class);
        $documents  = $authHelper->processUnguarded(
            function () use ($documentRepository, $order, $documentType, $data) {
                $documentRepository->uploadOrderDocuments($order->id, $documentType, $data);
            }
        );
    }
}
