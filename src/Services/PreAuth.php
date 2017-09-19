<?php

namespace Payone\Services;

use Payone\Adapter\Logger;
use Payone\Helpers\PaymentHelper;
use Payone\Models\Api\Response;
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
     * @return Response
     */
    public function executePreAuth(Basket $basket)
    {
        $selectedPaymentId = $basket->methodOfPaymentId;

        if (!$selectedPaymentId || !$this->paymentHelper->isPayonePayment($selectedPaymentId)) {
            throw new \Exception('No Payone payment method');
        }

        $preAuthResponse = $this->doPreAuth($basket);

        $payment = $this->createPayment($selectedPaymentId, $preAuthResponse, $basket);
        $this->paymentCache->storePayment((string) $selectedPaymentId, $payment);

        return $preAuthResponse;
    }

    /**
     * @param $selectedPaymentId
     * @param Response $preAuthResponse
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
                $basket
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
     * @return Response
     */
    private function doPreAuth(Basket $basket): Response
    {
        try {
            //TODO: have nice error messages for customer
            $preAuthResponse = $this->doPreAuthFromBasket($basket);
        } catch (\Exception $e) {
            $this->logger->logException($e);
            throw $e;
        }
        if (!($preAuthResponse instanceof Response) || !$preAuthResponse->getSuccess()) {
            throw new \Exception('The payment could not be executed! PreAuth request failed.');
        }

        return $preAuthResponse;
    }

    /**
     * @param Basket $basket
     *
     * @throws \Exception
     *
     * @return Response
     */
    private function doPreAuthFromBasket(Basket $basket)
    {
        $selectedPaymentId = $basket->methodOfPaymentId;
        $paymentCode = $this->paymentHelper->getPaymentCodeByMop($selectedPaymentId);
        $this->logger->setIdentifier(__METHOD__)->debug(
            'Api.doPreAuth',
            ['selectedPaymentId' => $selectedPaymentId, 'paymentCode' => $paymentCode]
        );

        $requestData = $this->preAuthDataProvider->getDataFromBasket($paymentCode, $basket, '');
        $response = $this->api->doPreAuth($requestData);

        return $response;
    }
}
