<?php

namespace Payone\Controllers;

use Payone\Adapter\Config as ConfigAdapter;
use Payone\Helpers\PaymentHelper;
use Payone\Migrations\CreatePaymentMethods;
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
     * @var PaymentHelper
     */
    private $paymentHelper;
    /**
     * @var CreatePaymentMethods
     */
    private $paymentMigration;

    /**
     * PaymentController constructor.
     *
     * @param Request $request
     * @param ConfigAdapter $config
     * @param PaymentHelper $paymentHelper
     * @param CreatePaymentMethods $paymentMigration
     */
    public function __construct(
        Request $request,
        ConfigAdapter $config,
        PaymentHelper $paymentHelper,
        CreatePaymentMethods $paymentMigration
    ) {
        $this->request = $request;
        $this->config = $config;
        $this->paymentHelper = $paymentHelper;
        $this->paymentMigration = $paymentMigration;
    }

    public function index()
    {
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

        $this->paymentHelper->updatePaymentStatus($reference, $txid, $txaction);

        echo 'TSOK';
    }
}
