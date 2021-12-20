<?php

namespace Payone\Controllers;

use Payone\Adapter\Logger;
use Payone\Methods\PayoneInvoiceSecurePaymentMethod;
use Payone\Services\PaymentCreation;
use Payone\Services\PaymentDocuments;
use Payone\Services\SettingsService;
use Plenty\Plugin\Controller;
use Plenty\Plugin\Http\Request;

/**
 * Class StatusController
 */
class StatusController extends Controller
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var SettingsService
     */
    protected $settingsService;

    /**
     * @var PaymentCreation
     */
    protected $paymentCreation;

    /**
     * @var PaymentDocuments
     */
    protected $paymentDocument;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * StatusController constructor.
     *
     * @param Request $request
     * @param SettingsService $settingsService
     * @param PaymentCreation $paymentCreation
     * @param PaymentDocuments $paymentDocument
     * @param Logger $logger
     */
    public function __construct(
        Request $request,
        SettingsService $settingsService,
        PaymentCreation $paymentCreation,
        PaymentDocuments $paymentDocument,
        Logger $logger
    ) {
        $this->request = $request;
        $this->settingsService = $settingsService;
        $this->paymentCreation = $paymentCreation;
        $this->paymentDocument = $paymentDocument;
        $this->logger = $logger;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function index()
    {
        $txid = $this->request->get('txid');
        $txaction = $this->request->get('txaction');
        $sequenceNumber = $this->request->get('sequencenumber');
        $transactionStatus = $this->request->get('transaction_status');
        if ($transactionStatus) {
            $txaction = $txaction . '_' . $transactionStatus;
        }

        $this->logger->setIdentifier(__METHOD__);
        $this->logger->addReference(Logger::PAYONE_REQUEST_REFERENCE, $txid);
        $this->logger->debug('Controller.Status', $this->request->all());

        if ($this->request->get('key') != md5($this->settingsService->getPaymentSettingsValue('key', PayoneInvoiceSecurePaymentMethod::PAYMENT_CODE)) &&
            $this->request->get('key') != md5($this->settingsService->getSettingsValue('key'))) {
            return 'ERROR';
        }

        if ($txaction === 'invoice') {
            $this
                ->paymentDocument
                ->addInvoiceToOrder($txid,
                    $this->request->get('invoiceid'),
                    $this->request->get('invoice_date'),
                    $this->request->get('invoice_grossamount'));
        } else {
            $this->paymentCreation->updatePaymentStatus($txid, $txaction, $sequenceNumber);
        }

        return 'TSOK';
    }
}
