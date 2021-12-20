<?php

namespace Payone\Providers\Api\Request;


class GetInvoiceDataProvider extends DataProviderAbstract
{
    /**
     * @param string $paymentCode
     * @param string|null $documentNumber
     * @param int|null $clientId
     * @param int|null $pluginSetId
     * @return array
     * @throws \Exception
     */
    public function getRequestData(string $paymentCode, string $documentNumber = null, int $clientId = null, int $pluginSetId = null): array
    {
        $requestParams = $this->getDefaultRequestData($paymentCode, $clientId, $pluginSetId);
        $requestParams['context']['documentNumber']   = $documentNumber;
        $this->validator->validate($requestParams);
        return $requestParams;
    }
}
