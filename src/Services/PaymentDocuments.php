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

    public function uploadDocument($txid, $invoiceId, $invoiceDate, $invoiceTotal)
    {
        $this->logger->setIdentifier(__METHOD__);

        $payments = $this->paymentRepository->getPaymentsByPropertyTypeAndValue(
            PaymentProperty::TYPE_TRANSACTION_ID,
            $txid,
            1
        );

        if (count($payments) !== 1) {
            //payment not found
            $this->logger->debug(
                'Api.doGetInvoice',
                'No payments found for txid (' . $txid . ')'
            );
            throw new \Exception('Payment not found.');
        }

        /* @var $payment Payment */
        $payment = array_shift($payments);

        $this->logger->debug('Api.doGetInvoice',
            [
                'taxid' => $txid,
                'invoiceId' => $invoiceId, 'invoiceDate' => $invoiceDate, 'invoiceTotal' => $invoiceTotal,
                'payment' => $payment
            ]
        );

        //only for secure invoice
        if ($this->paymentHelper->getMopId(PayoneInvoiceSecurePaymentMethod::PAYMENT_CODE) !=
            $payment->mopId) {
            return;
        }

        $orderId = $payment->order->orderId;

        if ((int)$orderId <= 0) {
            return;
        }

        if (substr($invoiceId, 0, 2) == 'GT') {
            //this is a credit note
            $authHelper = pluginApp(AuthHelper::class);
            $refundId = $authHelper->processUnguarded(
                function () use ($orderId, $invoiceTotal) {
                    $orderRepository = pluginApp(OrderRepositoryContract::class);
                    $order = $orderRepository->findOrderById($orderId);
                    foreach ($order->childOrders as $childOrder) {
                        if ($childOrder->typeId == OrderType::TYPE_CREDIT_NOTE &&
                            $childOrder->amount->invoiceTotal == abs($invoiceTotal)) {
                            return $childOrder->id;
                        }
                    }
                }
            );
            if ((int)$refundId == 0) {
                $this->logger->debug(
                    'Api.doGetInvoice',
                    'No credit-note found for invoiceId (' . $invoiceId . ')'
                );
                return;
            }
            $orderId = $refundId;
        }

        $requestData = $this->getInvoiceDataProvider
            ->getRequestData(PayoneInvoiceSecurePaymentMethod::PAYMENT_CODE, $invoiceId);

        $getInvoiceResult = $this->api->doGetInvoice($requestData);

        if (!$getInvoiceResult->getSuccess()) {
            $this->logger->error('Api.doGetInvoice',
                [
                    'invoiceId' => $invoiceId,
                    'payment' => $payment,
                    'errorMessage' => $getInvoiceResult->getErrorMessage(),
                ]
            );
            return;
        }

        $this->importInvoice($orderId, $invoiceId, $getInvoiceResult->getBase64(), $invoiceDate);
    }

    /**
     * Imports one invoice.
     *
     * @param int $orderId
     * @param array $invoice
     * @throws InvalidDocumentTypeException
     * @throws \Plenty\Exceptions\ValidationException
     */
    private function importInvoice(int $orderId, string $invoiceNumber, string $content, string $invoiceDate)
    {
        /** @var DocumentRepositoryContract $documentRepository */
        $documentRepository = pluginApp(DocumentRepositoryContract::class);

        // check if the document is already imported
        $documentRepository->setFilters([
            'numberWithPrefix' => $invoiceNumber,
            'orderId' => $orderId
        ]);

        /** @var PaginatedResult $result */
        $result = $documentRepository->find();

        if ($result->getTotalCount() > 0) {
            //document exists
            return;
        }

        $documentType = Document::INVOICE_EXTERNAL;
        if (substr($invoiceNumber, 0, 2) == 'GT') {
            $documentType = Document::CREDIT_NOTE_EXTERNAL;
        }

        $date = Carbon::now()->toW3cString();
        if (strlen($invoiceDate) == 8) {
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
        $authHelper->processUnguarded(
            function () use ($documentRepository, $orderId, $documentType, $data) {
                $documentRepository->uploadOrderDocuments($orderId, $documentType, $data);
            }
        );
    }
}
