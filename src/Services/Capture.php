<?php

namespace Payone\Services;

use Payone\Adapter\Config as ConfigAdapter;
use Payone\Adapter\Logger;
use Payone\Adapter\PaymentHistory;
use Payone\Helpers\PaymentHelper;
use Payone\Providers\Api\Request\CaptureDataProvider;
use Plenty\Modules\Order\Models\Order;
use Plenty\Modules\Payment\Contracts\PaymentRepositoryContract;
use Plenty\Modules\Payment\Models\Payment;
use Plenty\Modules\Payment\Models\PaymentProperty;

class Capture
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
     * @var Logger
     */
    private $logger;
    /**
     * @var PaymentHistory
     */
    private $paymentHistory;

    /**
     * @var ConfigAdapter
     */
    private $config;
    /**
     * @var CaptureDataProvider
     */
    private $captureDataProvider;
    /**
     * @var Api
     */
    private $api;
    /**
     * @var PaymentCreation
     */
    private $paymentCreation;

    /**
     * Capture constructor.
     *
     * @param PaymentRepositoryContract $paymentRepository
     * @param PaymentHelper $paymentHelper
     * @param Logger $logger
     * @param PaymentHistory $paymentHistory
     * @param ConfigAdapter $config
     * @param CaptureDataProvider $captureDataProvider
     * @param Api $api
     * @param PaymentCreation $paymentCreation
     */
    public function __construct(
        PaymentRepositoryContract $paymentRepository,
        PaymentHelper $paymentHelper,
        Logger $logger,
        PaymentHistory $paymentHistory,
        ConfigAdapter $config,
        CaptureDataProvider $captureDataProvider,
        Api $api,
        PaymentCreation $paymentCreation
    ) {
        $this->paymentRepository = $paymentRepository;
        $this->paymentHelper = $paymentHelper;
        $this->logger = $logger;
        $this->paymentHistory = $paymentHistory;
        $this->config = $config;
        $this->captureDataProvider = $captureDataProvider;
        $this->api = $api;
        $this->paymentCreation = $paymentCreation;
    }

    /**
     * @param Order $order
     */
    public function doCapture(Order $order)
    {
        $orderId = $order->id;
        $payments = $this->paymentRepository->getPaymentsByOrderId($orderId);
        $this->logger->setIdentifier(__METHOD__);

        /* @var $payment Payment */
        foreach ($payments as $payment) {
            $this->logger->info('Api.doCapture',
                [
                    'order' => $order,
                    'payment' => $payment,
                ]
            );
            if (!$this->paymentHelper->isPayonePayment($payment->mopId)) {
                continue;
            }

            $preAuthReference = $this->paymentHelper->getPaymentPropertyValue(
                $payment,
                PaymentProperty::TYPE_TRANSACTION_ID
            );

            if (!$preAuthReference) {
                $this->logger->error(
                    'Api.doCapture',
                    [
                        'order' => $order,
                        'payment' => $payment,
                        'payment_properties' => $payment->properties,
                    ]
                );
                //TODO: localise
                $text = 'Capture failed. No PreAuth';
                $this->paymentHistory->addPaymentHistoryEntry($payment, $text);
                continue;
            }

            $paymentCode = $this->paymentHelper->getPaymentCodeByMop($payment->mopId);
            $requestData = $this->captureDataProvider->getDataFromOrder($paymentCode, $order, $preAuthReference);

            $captureOrderResult = $this->api->doCapture($requestData);
            $text = 'Capture done. Transaction Id: ' . $captureOrderResult->getTransactionID();
            $this->paymentHistory->addPaymentHistoryEntry($payment, $text);

            if (!$captureOrderResult->getSuccess()) {
                $this->logger->error('Api.doCapture',
                    [
                        'order' => $order,
                        'payment' => $payment,
                        'preAuthReference' => $preAuthReference,
                        'errorMessage' => $captureOrderResult->getErrorMessage(),
                    ]
                );
                $text = 'Capture failed: ' . $captureOrderResult->getErrorMessage();
                $this->paymentHistory->addPaymentHistoryEntry($payment, $text);
                continue;
            }

            $this->paymentCreation->capturePayment($payment, $order);
        }
    }
}
