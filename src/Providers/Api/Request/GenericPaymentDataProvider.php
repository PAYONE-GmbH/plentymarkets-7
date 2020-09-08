<?php

namespace Payone\Providers\Api\Request;

class GenericPaymentDataProvider extends DataProviderAbstract
{
    /**
     * {@inheritdoc}
     */
    public function getGetConfigRequestData(string $paymentCode): array
    {
        $requestParams = $this->getDefaultRequestData($paymentCode);
        $requestParams['request'] = "genericpayment";
        $requestParams['addPaydata']['action'] = "getconfiguration";
        $requestParams['clearingtype'] = "wlt";
        $requestParams['wallettype'] = "AMZ";

        // Currency not mentioned in API-Doc of Payone
        $requestParams['currency'] = "EUR";



        $this->validator->validate($requestParams);
        return $requestParams;
    }
}
