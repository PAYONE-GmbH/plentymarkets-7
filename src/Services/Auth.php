<?php

namespace Payone\Services;

use Payone\Adapter\Logger;
use Payone\Helpers\PaymentHelper;
use Payone\Models\Api\AuthResponse;
use Payone\Models\PaymentCache;
use Payone\Providers\Api\Request\AuthDataProvider;
use Plenty\Modules\Basket\Models\Basket;
use Plenty\Modules\Payment\Models\Payment;

class Auth
{
    /**
     * @var PaymentHelper
     */
    protected $paymentHelper;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var PaymentCreation
     */
    protected $paymentCreationService;

    /**
     * @var SettingsService
     */
    protected $settingsService;

    /**
     * @var PaymentCache
     */
    protected $paymentCache;

    /**
     * @var AuthDataProvider
     */
    protected $authDataProvider;

    /**
     * @var Api
     */
    protected $api;

    /**
     * Auth constructor.
     *
     * @param PaymentHelper $paymentHelper
     * @param Logger $logger
     * @param PaymentCreation $paymentCreation
     * @param PaymentCache $paymentCache
     * @param Api $api
     * @param AuthDataProvider $authDataProvider
     */
    public function __construct(
        PaymentHelper $paymentHelper,
        Logger $logger,
        PaymentCreation $paymentCreation,
        PaymentCache $paymentCache,
        Api $api,
        AuthDataProvider $authDataProvider
    ) {
        $this->paymentHelper = $paymentHelper;
        $this->logger = $logger;
        $this->paymentCreationService = $paymentCreation;
        $this->paymentCache = $paymentCache;
        $this->api = $api;
        $this->authDataProvider = $authDataProvider;
    }

    /**
     * @param Basket $basket
     * @return AuthResponse
     * @throws \Exception
     */
    public function executeAuth(Basket $basket): AuthResponse
    {
        $selectedPaymentId = $basket->methodOfPaymentId;

        if (!$selectedPaymentId || !$this->paymentHelper->isPayonePayment($selectedPaymentId)) {
            throw new \Exception('No Payone payment method');
        }

        $authResponse = $this->doAuthFromBasket($basket);

        $payment = $this->createPayment($selectedPaymentId, $authResponse, $basket);
        $this->paymentCache->storePayment((string) $selectedPaymentId, $payment);
        $this->paymentCache->setActiveBasketId($basket->id);

        return $authResponse;
    }

    /**
     * @param $selectedPaymentId
     * @param AuthResponse $authResponse
     * @throws \Exception
     * @return Payment
     */
    private function createPayment($selectedPaymentId, $authResponse, $basket): Payment
    {
        try {
            $plentyPayment = $this->paymentCreationService->createPayment(
                $selectedPaymentId,
                $authResponse,
                $basket,
                $authResponse->getClearing()
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
     * @throws \Exception
     * @return AuthResponse
     */
    private function doAuthFromBasket(Basket $basket): AuthResponse
    {
        $selectedPaymentId = $basket->methodOfPaymentId;
        $paymentCode = $this->paymentHelper->getPaymentCodeByMop($selectedPaymentId);
        $this->logger->setIdentifier(__METHOD__)->debug(
            'Api.doAuth',
            ['selectedPaymentId' => $selectedPaymentId, 'paymentCode' => $paymentCode]
        );

        $requestData = $this->authDataProvider->getDataFromBasket($paymentCode, $basket, '');
        $this->logger->setIdentifier(__METHOD__)->debug(
            'Api.doAuth',
            ['requestData' => $requestData]
        );
        try {
            $authResponse = $this->api->doAuth($requestData);
        } catch (\Exception $e) {
            $this->logger->logException($e);
            throw $e;
        }
        if (!($authResponse instanceof AuthResponse) || !$authResponse->getSuccess()) {
            throw new \Exception('The payment could not be executed! Auth request failed.');
        }

        return $authResponse;
    }
}
