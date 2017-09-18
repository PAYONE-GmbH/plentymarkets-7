<?php

namespace Payone\Services;

use Payone\Adapter\Config as ConfigAdapter;
use Payone\Adapter\Logger;
use Payone\Adapter\PaymentHistory;
use Payone\Helpers\PaymentHelper;
use Payone\Models\Api\Response;
use Payone\Models\PaymentCache;
use Payone\Providers\Api\Request\AuthDataProvider;
use Plenty\Modules\Basket\Models\Basket;
use Plenty\Modules\Order\Contracts\OrderRepositoryContract;
use Plenty\Modules\Payment\Contracts\PaymentRepositoryContract;
use Plenty\Modules\Payment\Models\Payment;

class Auth
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
     * @var AuthDataProvider
     */
    private $authDataProvider;
    /**
     * @var Api
     */
    private $api;

    /**
     * ReAuth constructor.
     *
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
        PaymentRepositoryContract $paymentRepository,
        PaymentHelper $paymentHelper,
        Logger $logger,
        PaymentHistory $paymentHistory,
        PaymentCreation $paymentCreation,
        OrderRepositoryContract $orderRepositoryContract,
        ConfigAdapter $config,
        PaymentCache $paymentCache,
        Api $api,
        AuthDataProvider $authDataProvider
    ) {
        $this->paymentRepository = $paymentRepository;
        $this->paymentHelper = $paymentHelper;
        $this->logger = $logger;
        $this->paymentHistory = $paymentHistory;
        $this->paymentCreationService = $paymentCreation;
        $this->orderRepo = $orderRepositoryContract;
        $this->config = $config;
        $this->paymentCache = $paymentCache;
        $this->api = $api;
        $this->authDataProvider = $authDataProvider;
    }

    /**
     * @param Basket $basket
     *
     * @return Response
     */
    public function executeAuth(Basket $basket)
    {
        $selectedPaymentId = $basket->methodOfPaymentId;

        if (!$selectedPaymentId || !$this->paymentHelper->isPayonePayment($selectedPaymentId)) {
            throw new \Exception('No Payone payment method');
        }

        $authResponse = $this->doAuth($basket);

        $payment = $this->createPayment($selectedPaymentId, $authResponse, $basket);
        $this->paymentCache->storePayment((string) $selectedPaymentId, $payment);

        return $authResponse;
    }

    /**
     * @param $selectedPaymentId
     * @param Response $authResponse
     * @param $orderId
     *
     * @throws \Exception
     *
     * @return Payment
     */
    private function createPayment($selectedPaymentId, $authResponse, $basket): Payment
    {
        try {
            $plentyPayment = $this->paymentCreationService->createPayment(
                $selectedPaymentId,
                $authResponse,
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
    private function doAuth(Basket $basket): Response
    {
        try {
            //TODO: have nice error messages for customer
            $authResponse = $this->doAuthFromBasket($basket);
        } catch (\Exception $e) {
            $this->logger->logException($e);
            throw $e;
        }
        if (!($authResponse instanceof Response) || !$authResponse->getSuccess()) {
            throw new \Exception('The payment could not be executed! Auth request failed.');
        }

        return $authResponse;
    }

    /**
     * @param Basket $basket
     *
     * @throws \Exception
     *
     * @return Response
     */
    private function doAuthFromBasket(Basket $basket)
    {
        $selectedPaymentId = $basket->methodOfPaymentId;
        $paymentCode = $this->paymentHelper->getPaymentCodeByMop($selectedPaymentId);
        $this->logger->setIdentifier(__METHOD__)->debug(
            'Api.doAuth',
            ['selectedPaymentId' => $selectedPaymentId, 'paymentCode' => $paymentCode]
        );

        $requestData = $this->authDataProvider->getDataFromBasket($paymentCode, $basket, '');
        $response = $this->api->doAuth($requestData);

        return $response;
    }
}
