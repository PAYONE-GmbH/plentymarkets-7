<?php

namespace Payone\Services;

use Payone\Adapter\Config as ConfigAdapter;
use Payone\Adapter\Logger;
use Payone\Adapter\PaymentHistory;
use Payone\Helpers\PaymentHelper;
use Payone\Models\Api\Response;
use Payone\Models\PaymentCache;
use Payone\Providers\Api\Request\PreAuthDataProvider;
use Plenty\Modules\Basket\Models\Basket;
use Plenty\Modules\Order\Contracts\OrderRepositoryContract;
use Plenty\Modules\Payment\Contracts\PaymentRepositoryContract;
use Plenty\Modules\Payment\Models\Payment;

class PreAuth
{
    /**
     * @var PaymentService
     */
    private $paymentService;
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
     * @var PaymentCreation
     */
    private $paymentCreationService;
    /**
     * @var OrderRepositoryContract
     */
    private $orderRepo;
    /**
     * @var ConfigAdapter
     */
    private $config;
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
     * ReAuth constructor.
     *
     * @param PaymentService $paymentService
     * @param PaymentRepositoryContract $paymentRepository
     * @param PaymentHelper $paymentHelper
     * @param Logger $logger
     * @param PaymentHistory $paymentHistory
     * @param PaymentCreation $paymentCreation
     * @param OrderRepositoryContract $orderRepositoryContract
     * @param ConfigAdapter $config
     * @param PaymentCache $paymentCache
     */
    public function __construct(
        PaymentService $paymentService,
        PaymentRepositoryContract $paymentRepository,
        PaymentHelper $paymentHelper,
        Logger $logger,
        PaymentHistory $paymentHistory,
        PaymentCreation $paymentCreation,
        OrderRepositoryContract $orderRepositoryContract,
        ConfigAdapter $config,
        PaymentCache $paymentCache,
        Api $api,
        PreAuthDataProvider $preAuthDataProvider
    ) {
        $this->paymentService = $paymentService;
        $this->paymentRepository = $paymentRepository;
        $this->paymentHelper = $paymentHelper;
        $this->logger = $logger;
        $this->paymentHistory = $paymentHistory;
        $this->paymentCreationService = $paymentCreation;
        $this->orderRepo = $orderRepositoryContract;
        $this->config = $config;
        $this->paymentCache = $paymentCache;
        $this->api = $api;
        $this->preAuthDataProvider = $preAuthDataProvider;
    }

    public function executePreAuth(Basket $basket)
    {
        $selectedPaymentId = $basket->methodOfPaymentId;

        if (!$selectedPaymentId || !$this->paymentHelper->isPayonePayment($selectedPaymentId)) {
            return;
        }

        $preAuthResponse = $this->doPreAuth($basket);

        $payment = $this->createPayment($selectedPaymentId, $preAuthResponse, $basket);
        $this->paymentCache->storePayment((string) $selectedPaymentId, $payment);
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
