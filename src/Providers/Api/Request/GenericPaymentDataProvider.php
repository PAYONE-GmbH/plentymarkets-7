<?php

namespace Payone\Providers\Api\Request;

use Payone\Helpers\ShopHelper;
use Payone\Methods\PayoneAmazonPayPaymentMethod;
use Payone\Providers\Api\Request\Models\GenericPayment;
use Plenty\Modules\Basket\Models\Basket;

class GenericPaymentDataProvider extends DataProviderAbstract
{

    /**
     * @param string $paymentCode
     * @param int|null $clientId
     * @param int|null $pluginSetId
     * @return array
     */
    private function getDefaultPaymentRequestData(string $paymentCode, int $clientId = null, int $pluginSetId = null): array
    {
        $requestParams = $this->getDefaultRequestData($paymentCode, $clientId, $pluginSetId);
        $requestParams['request'] = GenericPayment::REQUEST_TYPE;

        if ($paymentCode == PayoneAmazonPayPaymentMethod::PAYMENT_CODE) {
            $requestParams['clearingtype'] = PayoneAmazonPayPaymentMethod::CLEARING_TYPE;
            $requestParams['wallettype'] = PayoneAmazonPayPaymentMethod::CLEARING_TYPE;
        }

        return $requestParams;
    }

    /**
     * @param string $paymentCode
     * @param string $currency
     * @param int|null $clientId
     * @param int|null $pluginSetId
     * @return array
     * @throws \Exception
     */
    public function getGetConfigRequestData(string $paymentCode, string $currency, int $clientId = null, int $pluginSetId = null): array
    {
        $requestParams = $this->getDefaultPaymentRequestData($paymentCode, $clientId, $pluginSetId);

        // Currency not mentioned in API-Doc of Payone
        $requestParams['currency'] = $currency;

        $requestParams['add_paydata']['action'] = GenericPayment::ACTIONTYPE_GETCONFIGURATION;

        $this->validator->validate($requestParams);
        return $requestParams;
    }

    /**
     * @param string $paymentCode
     * @param string $workOrderId
     * @param string $amazonAddressToken
     * @param string $amazonReferenceId
     * @param string $currency
     * @param string $amount
     * @param int|null $clientId
     * @param int|null $pluginSetId
     * @return array
     * @throws \Exception
     */
    public function getGetOrderReferenceDetailsRequestData(
        string $paymentCode,
        string $workOrderId,
        string $amazonAddressToken,
        string $amazonReferenceId,
        string $currency,
        string $amount,
        int $clientId = null,
        int $pluginSetId = null
    ): array {
        $requestParams = $this->getDefaultPaymentRequestData($paymentCode, $clientId, $pluginSetId);

        // Currency not mentioned in API-Doc of Payone
        $requestParams['currency'] = $currency;
        // amount in smallest unit
        $requestParams['amount'] = $amount * 100;


        $requestParams['add_paydata']['action'] = GenericPayment::ACTIONTYPE_GETORDERREFERENCEDETAILS;
        $requestParams['add_paydata']['amazon_address_token'] = $amazonAddressToken;
        $requestParams['add_paydata']['amazon_reference_id'] = $amazonReferenceId;
        $requestParams['workorderid'] = $workOrderId;

        $this->validator->validate($requestParams);
        return $requestParams;
    }

    /**
     * @param string $paymentCode
     * @param string $workOrderId
     * @param string $amazonReferenceId
     * @param string $amount
     * @param string|null $currency
     * @param int|null $clientId
     * @param int|null $pluginSetId
     * @return array
     * @throws \Exception
     */
    public function getSetOrderReferenceDetailsRequestData(
        string $paymentCode,
        string $workOrderId,
        string $amazonReferenceId,
        string $amount,
        string $currency = null,
        int $clientId = null,
        int $pluginSetId = null
    ): array {
        $requestParams = $this->getDefaultPaymentRequestData($paymentCode, $clientId, $pluginSetId);

        if(!is_null($currency)) {
            // Currency not mentioned in API-Doc of Payone
            $requestParams['currency'] = $currency;
        }
        // amount in smallest unit
        $requestParams['amount'] = $amount * 100;

        $requestParams['add_paydata']['action'] = GenericPayment::ACTIONTYPE_SETORDERREFERENCEDETAILS;
        $requestParams['add_paydata']['amazon_reference_id'] = $amazonReferenceId;
        $requestParams['workorderid'] = $workOrderId;

        $this->validator->validate($requestParams);
        return $requestParams;
    }

    /**
     * @param string $paymentCode
     * @param string $workOrderId
     * @param string $reference
     * @param string $amazonReferenceId
     * @param string $amount
     * @param string $currency
     * @param string $basketId
     * @param int|null $clientId
     * @param int|null $pluginSetId
     * @return array
     * @throws \Exception
     */
    public function getConfirmOrderReferenceRequestData(
        string $paymentCode,
        string $workOrderId,
        string $reference,
        string $amazonReferenceId,
        string $amount,
        string $currency,
        string $basketId,
        int $clientId = null,
        int $pluginSetId = null
    ): array {
        $requestParams = $this->getDefaultPaymentRequestData($paymentCode, $clientId, $pluginSetId);

        // Currency not mentioned in API-Doc of Payone
        $requestParams['currency'] = $currency;
        // amount in smallest unit
        $requestParams['amount'] = $amount * 100;

        $requestParams['add_paydata']['action'] = GenericPayment::ACTIONTYPE_CONFIRMORDERREFERENCE;
        $requestParams['add_paydata']['reference'] = $reference;
        $requestParams['add_paydata']['amazon_reference_id'] = $amazonReferenceId;
        $requestParams['workorderid'] = $workOrderId;

        /** @var ShopHelper $shopHelper */
        $shopHelper = pluginApp(ShopHelper::class);

        $successParam = '';
        if (strlen($basketId)) {
            $successParam = '?transactionBasketId=' . $basketId;
        }

        $requestParams['successurl'] = $shopHelper->getPlentyDomain() . '/payment/payone/checkoutSuccess' . $successParam;
        $requestParams['errorurl'] = $shopHelper->getPlentyDomain() . '/payment/payone/error';

        $this->validator->validate($requestParams);
        return $requestParams;
    }

    /**
     * @param string $paymentCode
     * @param Basket $basket
     * @return array
     * @throws \Exception
     */
    public function getStartSessionRequestData(string $paymentCode, Basket $basket)
    {

        $requestParams = $this->getDefaultPaymentRequestData($paymentCode);

        // Currency not mentioned in API-Doc of Payone
        $requestParams['currency'] = $basket->currency;
        // amount in smallest unit
        $requestParams['amount'] = ($basket->basketAmount) * 100;

        $requestParams['add_paydata']['action'] = GenericPayment::ACTIONTYPE_STARTSESSION;
        /** @var ShopHelper $shopHelper */
        $shopHelper = pluginApp(ShopHelper::class);

        $successParam = '';
        $basketId = $basket->id;
        if (strlen($basketId)) {
            $successParam = '?transactionBasketId=' . $basketId;
        }

        $requestParams['basket'] = $this->getBasketData($basket);
        $requestParams['basketItems'] = $this->getCartItemData($basket);

        $requestParams['successurl'] = $shopHelper->getPlentyDomain() . '/payment/payone/checkoutSuccess' . $successParam;
        $requestParams['errorurl'] = $shopHelper->getPlentyDomain() . '/payment/payone/error';
        $requestParams['backurl'] = $shopHelper->getPlentyDomain() . '/payment/payone/back';
        $this->validator->validate($requestParams);
        return $requestParams;
    }

    /**
     * @param string $paymentCode
     * @param Basket $basket
     * @return array
     * @throws \Exception
     */
    public function getStartSessionRequestDataFromOrder(string $paymentCode, $order)
    {


        $requestParams = $this->getDefaultPaymentRequestData($paymentCode);

        // Currency not mentioned in API-Doc of Payone
        $requestParams['currency'] = $order->currency;
        // amount in smallest unit
        $requestParams['amount'] = ($order->amount->invoiceTotal) * 100;

        $requestParams['add_paydata']['action'] = GenericPayment::ACTIONTYPE_STARTSESSION;
        /** @var ShopHelper $shopHelper */
        $shopHelper = pluginApp(ShopHelper::class);

        $successParam = '';
        $basketId = $order->id;
        if (strlen($basketId)) {
            $successParam = '?transactionBasketId=' . $basketId;
        }

        $requestParams['basket'] = $this->getBasketDataFromOrder($order);
        $requestParams['basketItems'] = $this->getOrderItemData($order);

        $requestParams['successurl'] = $shopHelper->getPlentyDomain() . '/payment/payone/checkoutSuccessReinit';
        $requestParams['errorurl'] = $shopHelper->getPlentyDomain() . '/payment/payone/error';
        $requestParams['backurl'] = $shopHelper->getPlentyDomain() . '/payment/payone/back';
        $this->validator->validate($requestParams);

        return $requestParams;
    }
}
