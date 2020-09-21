<?php

namespace Payone\Providers\Api\Request;

use Payone\Helpers\ShopHelper;
use Payone\Methods\PayoneAmazonPayPaymentMethod;
use Payone\Providers\Api\Request\Models\GenericPayment;

class GenericPaymentDataProvider extends DataProviderAbstract
{

    private function getDefaultPaymentRequestData(string $paymentCode)
    {
        $requestParams = $this->getDefaultRequestData($paymentCode);
        $requestParams['request'] = GenericPayment::REQUEST_TYPE;

        if ($paymentCode == PayoneAmazonPayPaymentMethod::PAYMENT_CODE) {
            $requestParams['clearingtype'] = PayoneAmazonPayPaymentMethod::CLEARING_TYPE;
            $requestParams['wallettype'] = PayoneAmazonPayPaymentMethod::CLEARING_TYPE;
        }

        return $requestParams;
    }
    /**
     * {@inheritdoc}
     */
    public function getGetConfigRequestData(string $paymentCode): array
    {
        $requestParams = $this->getDefaultPaymentRequestData($paymentCode);

        // Currency not mentioned in API-Doc of Payone
        $requestParams['currency'] = "EUR";

        $requestParams['add_paydata']['action'] = GenericPayment::ACTIONTYPE_GETCONFIGURATION;

        $this->validator->validate($requestParams);
        return $requestParams;
    }

    /**
     * {@inheritdoc}
     */
    public function getGetOrderReferenceDetailsRequestData(string $paymentCode,
                                                           string $workOrderId,
                                                           string $amazonAddressToken,
                                                           string $amazonReferenceId): array
    {
        $requestParams = $this->getDefaultPaymentRequestData($paymentCode);

        // Currency not mentioned in API-Doc of Payone
        $requestParams['currency'] = "EUR";


        $requestParams['add_paydata']['action'] = GenericPayment::ACTIONTYPE_GETORDERREFERENCEDETAILS;
        $requestParams['add_paydata']['amazon_address_token'] = $amazonAddressToken;
        $requestParams['add_paydata']['amazon_reference_id'] = $amazonReferenceId;
        $requestParams['workorderid'] = $workOrderId;

        $this->validator->validate($requestParams);
        return $requestParams;
    }

    /**
     * {@inheritdoc}
     */
    public function getSetOrderReferenceDetailsRequestData(string $paymentCode,
                                                           string $workOrderId,
                                                           string $amazonReferenceId,
                                                           string $currency,
                                                           string $amount): array
    {
        $requestParams = $this->getDefaultPaymentRequestData($paymentCode);

        // Currency not mentioned in API-Doc of Payone
        $requestParams['currency'] = $currency;
        // amount in smallest unit
        $requestParams['amount'] = $amount * 100;


        $requestParams['add_paydata']['action'] = GenericPayment::ACTIONTYPE_SETORDERREFERENCEDETAILS;
        $requestParams['add_paydata']['amazon_reference_id'] = $amazonReferenceId;
        $requestParams['workorderid'] = $workOrderId;

        $this->validator->validate($requestParams);
        return $requestParams;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfirmOrderReferenceRequestData(string $paymentCode,
                                                        string $workOrderId,
                                                        $reference,
                                                        string $amazonReferenceId,
                                                        string $amount,
                                                        string $basketId)
    {
        $requestParams = $this->getDefaultPaymentRequestData($paymentCode);

        // Currency not mentioned in API-Doc of Payone
        $requestParams['currency'] = "EUR";
        // amount in smallest unit
        $requestParams['amount'] = $amount;

        $requestParams['add_paydata']['action'] = GenericPayment::ACTIONTYPE_CONFIRMORDERREFERENCE;
        $requestParams['add_paydata']['reference'] = $reference;
        $requestParams['add_paydata']['amazon_reference_id'] = $amazonReferenceId;
        $requestParams['workorderid'] = $workOrderId;

        /** @var ShopHelper $shopHelper */
        $shopHelper = pluginApp(ShopHelper::class);

        $successParam = '';
        if(strlen($basketId)){
            $successParam = '?transactionBasketId='.$basketId;
        }

        $requestParams['successurl'] = $shopHelper->getPlentyDomain() . '/payment/payone/checkoutSuccess' . $successParam;
        $requestParams['errorurl'] = $shopHelper->getPlentyDomain() . '/payment/payone/error';

        $this->validator->validate($requestParams);
        return $requestParams;
    }
}
