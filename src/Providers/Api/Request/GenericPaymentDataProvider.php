<?php

namespace Payone\Providers\Api\Request;

use Payone\Helpers\ShopHelper;
use Payone\Providers\Api\Request\Models\GenericPayment;

class GenericPaymentDataProvider extends DataProviderAbstract
{
    /**
     * {@inheritdoc}
     */
    public function getGetConfigRequestData(string $paymentCode): array
    {
        $requestParams = $this->getDefaultRequestData($paymentCode);
        $requestParams['request'] = GenericPayment::REQUEST_TYPE;
        $requestParams['clearingtype'] = "wlt";
        $requestParams['wallettype'] = "AMZ";
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
        // TODO: Maybe load creds from cache/session ?

        $requestParams = $this->getDefaultRequestData($paymentCode);
        $requestParams['request'] = GenericPayment::REQUEST_TYPE;
        $requestParams['clearingtype'] = "wlt";
        $requestParams['wallettype'] = "AMZ";
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
                                                           //string $amazonAddressToken,
                                                        //   string $storename,
                                                           string $amount): array
    {
        // TODO: Maybe load creds from cache/session ?

        $requestParams = $this->getDefaultRequestData($paymentCode);
        $requestParams['request'] = GenericPayment::REQUEST_TYPE;
        $requestParams['clearingtype'] = "wlt";
        $requestParams['wallettype'] = "AMZ";
        // Currency not mentioned in API-Doc of Payone
        $requestParams['currency'] = "EUR";
        // amount in smallest unit
        $requestParams['amount'] = $amount;


        $requestParams['add_paydata']['action'] = GenericPayment::ACTIONTYPE_SETORDERREFERENCEDETAILS;
        $requestParams['add_paydata']['amazon_reference_id'] = $amazonReferenceId;
        //$requestParams['add_paydata']['amazon_address_token'] = $amazonAddressToken;
        //$requestParams['add_paydata']['storename'] = $storename;
        $requestParams['workorderid'] = $workOrderId;

        $this->validator->validate($requestParams);
        return $requestParams;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfirmOrderReferenceRequestData(string $paymentCode, string $workOrderId, $reference, string $amazonReferenceId, string $amount)
    {
        $requestParams = $this->getDefaultRequestData($paymentCode);
        $requestParams['request'] = GenericPayment::REQUEST_TYPE;
        $requestParams['clearingtype'] = "wlt";
        $requestParams['wallettype'] = "AMZ";
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

        $requestParams['successurl'] = $shopHelper->getPlentyDomain() . '/payment/payone/checkoutSuccess';
        $requestParams['errorurl'] = $shopHelper->getPlentyDomain() . '/payment/payone/checkoutSuccess';

        return $requestParams;
    }
}
