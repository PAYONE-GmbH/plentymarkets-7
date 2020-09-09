<?php

namespace Payone\Providers\Api\Request;

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
                                                           string $amazonReferenceId,
                                                           string $amazonAddressToken): array
    {
        // TODO: Maybe load creds from cache/session ?

        $requestParams = $this->getDefaultRequestData($paymentCode);
        $requestParams['request'] = GenericPayment::REQUEST_TYPE;
        $requestParams['clearingtype'] = "wlt";
        $requestParams['wallettype'] = "AMZ";
        // Currency not mentioned in API-Doc of Payone
        $requestParams['currency'] = "EUR";


        $requestParams['add_paydata']['action'] = GenericPayment::ACTIONTYPE_GETORDERREFERENCEDETAILS;
        $requestParams['add_paydata']['amazon_address_token'] = $amazonReferenceId;
        $requestParams['add_paydata']['amazon_reference_id'] = $amazonAddressToken;
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
                                                           string $amazonAddressToken,
                                                           string $storename,
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
        $requestParams['add_paydata']['amazon_address_token'] = $amazonReferenceId;
        $requestParams['add_paydata']['amazon_reference_id'] = $amazonAddressToken;
        $requestParams['add_paydata']['storename'] = $storename;
        $requestParams['workorderid'] = $workOrderId;

        $this->validator->validate($requestParams);
        return $requestParams;
    }
}
