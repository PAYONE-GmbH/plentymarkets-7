<?php

namespace Payone\Controllers;

use Payone\Adapter\Config as ConfigAdapter;
use Payone\Adapter\Logger;
use Payone\Migrations\CreatePaymentMethods;
use Payone\Services\PaymentCreation;
use Plenty\Plugin\Controller;
use Plenty\Plugin\Http\Request;

/**
 * Class ConfigController
 */
class StatusController extends Controller
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var ConfigAdapter
     */
    private $config;

    /**
     * @var PaymentCreation
     */
    private $paymentCreation;
    /**
     * @var CreatePaymentMethods
     */
    private $paymentMigration;
    /**
     * @var Logger
     */
    private $logger;

    /**
     * StatusController constructor.
     *
     * @param Request $request
     * @param ConfigAdapter $config
     * @param PaymentCreation $paymentCreation
     * @param CreatePaymentMethods $paymentMigration
     * @param Logger $logger
     */
    public function __construct(
        Request $request,
        ConfigAdapter $config,
        PaymentCreation $paymentCreation,
        CreatePaymentMethods $paymentMigration,
        Logger $logger
    ) {
        $this->request = $request;
        $this->config = $config;
        $this->paymentCreation = $paymentCreation;
        $this->paymentMigration = $paymentMigration;
        $this->logger = $logger;
    }

    public function index()
    {
        $this->logger->setIdentifier(__METHOD__)->debug('Controller.Status', $this->request->all());
        if ($this->request->get('key') != md5($this->config->get('key'))) {
            return;
        }
        $txid = $this->request->get('txid');
        $reference = $this->request->get('reference');
        $txaction = $this->request->get('txaction');
        $transactionStatus = $this->request->get('transaction_status');
        if ($transactionStatus) {
            $txaction = $txaction . '_' . $transactionStatus;
        }

        $this->paymentCreation->updatePaymentStatus($reference, $txid, $txaction);

        echo 'TSOK';
    }
}
