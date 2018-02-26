<?php

namespace Payone\Services;

use Payone\Adapter\Logger;
use Payone\Helpers\PaymentHelper;
use Payone\Models\Api\ManagemandateResponse;
use Payone\Models\Api\ResponseAbstract;
use Payone\Providers\Api\Request\ManagemandateDataProvider;
use Plenty\Modules\Basket\Models\Basket;

class SepaMandate
{
    private $api;
    private $requestDataProvider;
    private $logger;
    private $paymentHelper;

    /**
     * SepaMandate constructor.
     *
     * @param Api $api
     * @param ManagemandateDataProvider $requestDataProvider
     * @param Logger $logger
     * @param PaymentHelper $helper
     */
    public function __construct(
        Api $api,
        ManagemandateDataProvider $requestDataProvider,
        Logger $logger,
        PaymentHelper $helper
    ) {
        $this->api = $api;
        $this->requestDataProvider = $requestDataProvider;
        $this->logger = $logger;
        $this->paymentHelper = $helper;
    }

    /**
     * @param Basket $basket
     *
     * @throws \Exception
     *
     * @return ManagemandateResponse
     */
    public function createMandate(Basket $basket)
    {
        $selectedPaymentId = $basket->methodOfPaymentId;
        $paymentCode = $this->paymentHelper->getPaymentCodeByMop($selectedPaymentId);
        $this->logger->setIdentifier(__METHOD__)->debug(
            'SepaMandate.createMandate',
            ['selectedPaymentId' => $selectedPaymentId, 'paymentCode' => $paymentCode]
        );

        $requestData = $this->requestDataProvider->getDataFromBasket($paymentCode, $basket, '');
        try {
            $response = $this->api->doManagemandate($requestData);
        } catch (\Exception $e) {
            $this->logger->logException($e);
            throw $e;
        }
        if (!($response instanceof ResponseAbstract) || !$response->getSuccess()) {
            throw new \Exception($response->getErrorMessage() ?? 'Mandate could not be created. Request failed.');
        }

        return $response;
    }
}
