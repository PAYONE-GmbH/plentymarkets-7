<?php

namespace Payone\Services;

use Payone\Adapter\Logger;
use Payone\Helpers\PaymentHelper;
use Payone\Models\Api\PreAuthResponse;
use Payone\Models\Api\Response;
use Payone\Models\Api\ResponseAbstract;
use Payone\Models\PaymentCache;
use Payone\Providers\Api\Request\PreAuthDataProvider;
use Plenty\Modules\Basket\Models\Basket;
use Plenty\Modules\Payment\Models\Payment;

class PreAuth
{
    /**
     * @var PaymentHelper
     */
    private $paymentHelper;
    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var PaymentCreation
     */
    private $paymentCreationService;

    /**
     * @var PaymentCache
     */
    private $paymentCache;

    /**
     * @var PreAuthDataProvider
     */
    private $preAuthDataProvider;
    /**
     * @var Api
     */
    private $api;

    /**
     * PreAuth constructor.
     *
     * @param PaymentHelper $paymentHelper
     * @param Logger $logger
     * @param PaymentCreation $paymentCreation
     * @param PaymentCache $paymentCache
     * @param Api $api
     * @param PreAuthDataProvider $preAuthDataProvider
     */
    public function __construct(
        PaymentHelper $paymentHelper,
        Logger $logger,
        PaymentCreation $paymentCreation,
        PaymentCache $paymentCache,
        Api $api,
        PreAuthDataProvider $preAuthDataProvider
    ) {
        $this->paymentHelper = $paymentHelper;
        $this->logger = $logger;
        $this->paymentCreationService = $paymentCreation;
        $this->paymentCache = $paymentCache;
        $this->api = $api;
        $this->preAuthDataProvider = $preAuthDataProvider;
    }

    /**
     * @param Basket $basket
     *
     * @return PreAuthResponse
     */
    public function executePreAuth(Basket $basket)
    {
        $selectedPaymentId = $basket->methodOfPaymentId;

        if (!$selectedPaymentId || !$this->paymentHelper->isPayonePayment($selectedPaymentId)) {
            throw new \Exception('No Payone payment method');
        }

        $preAuthResponse = $this->doPreAuthFromBasket($basket);

        $payment = $this->createPayment($selectedPaymentId, $preAuthResponse, $basket);
        $this->paymentCache->storePayment((string) $selectedPaymentId, $payment);

        return $preAuthResponse;
    }

    /**
     * @param $selectedPaymentId
     * @param PreAuthResponse $preAuthResponse
     * @param $orderId
     *
     * @throws \Exception
     *
     * @return Payment
     */
    private function createPayment($selectedPaymentId, $preAuthResponse, $basket): Payment
    {
        try {
            $plentyPayment = $this->paymentCreationService->createPayment(
                $selectedPaymentId,
                $preAuthResponse,
                $basket,
                $preAuthResponse->getClearing()
            );
            if (!$plentyPayment instanceof Payment) {
                throw new \Exception('Not an instance of Payment');
            }
        } catch (\Exception $e) {
            $this->logger->logException($e);
            throw new \Exception('The payment could not be created: ' . PHP_EOL . $e->getMessage());
        }

        return $plentyPayment;
    }

    /**
     * @param Basket $basket
     *
     * @throws \Exception
     *
     * @return PreAuthResponse
     */
    private function doPreAuthFromBasket(Basket $basket)
    {
        $selectedPaymentId = $basket->methodOfPaymentId;
        $paymentCode = $this->paymentHelper->getPaymentCodeByMop($selectedPaymentId);
        $this->logger->setIdentifier(__METHOD__)->debug(
            'Api.doPreAuth',
            ['selectedPaymentId' => $selectedPaymentId, 'paymentCode' => $paymentCode]
        );
        try {
            $requestData = $this->preAuthDataProvider->getDataFromBasket($paymentCode, $basket, '');
            $preAuthResponse = $this->api->doPreAuth($requestData);
        } catch (\Exception $e) {
            $this->logger->logException($e);
            throw $e;
        }
        if (!($preAuthResponse instanceof PreAuthResponse) || !$preAuthResponse->getSuccess()) {
            throw new \Exception('The payment could not be executed! PreAuth request failed.');
        }

        return $preAuthResponse;
    }
}
