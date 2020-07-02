<?php

namespace Payone\Providers\Api\Request;


class GetInvoiceDataProvider extends DataProviderAbstract
{
    /**
     * {@inheritdoc}
     */
    public function getRequestData(string $paymentCode,
                                   string $requestReference = null,
                                   string $sequenceNumber = null,
                                   string $documentType = null)
    {
        $requestParams = $this->getDefaultRequestData($paymentCode);
        $requestParams['context']['documentNumber']   = $documentType.'-'.$requestReference.'-'.$sequenceNumber;
        $this->validator->validate($requestParams);
        return $requestParams;
    }
}
