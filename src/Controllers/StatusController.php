<?php

namespace Payone\Controllers;

use Payone\Helper\PaymentHelper;
use Plenty\Plugin\ConfigRepository;
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
     * @var ConfigRepository
     */
    private $config;

    /**
     * @var PaymentHelper
     */
    private $paymentHelper;


    /**
     * PaymentController constructor.
     *
     * @param Request $request
     * @param ConfigRepository $config
     * @param PaymentHelper $paymentHelper
     */
    public function __construct(
        Request $request,
        ConfigRepository $config,
        PaymentHelper $paymentHelper
    ) {
        $this->request = $request;
        $this->config = $config;
        $this->paymentHelper = $paymentHelper;
    }

    /**
     * @return void
     */
    public function index()
    {
        if ($this->request->get("key") != hash("md5", $this->config->get('key'))) {
            return;
        }
        $txid = $this->request->get("txid");
        $reference = $this->request->get("reference");
        $txaction = $this->request->get("txaction");
        $transactionStatus = $this->request->get("transaction_status");
        if ($transactionStatus) {
            $txaction = $txaction . '_' . $transactionStatus;
        }

        $this->paymentHelper->updatePaymentStatus($reference, $txid, $txaction);

        echo "TSOK";
    }

}
