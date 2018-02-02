<?php

namespace Payone\Models\Api;

use Payone\Models\SepaMandate;

/**
 * Class ManagemandateResponseFactory
 */
class ManagemandateResponseFactory
{
    /**
     * @param array $responseData
     *
     * @return ManagemandateResponse
     */
    public static function create(array $responseData)
    {
        /** @var SepaMandate $mandate */
        $mandate = pluginApp(SepaMandate::class);
        $mandate->init(
            $responseData['responseData']['mandate_identification'],
            $responseData['responseData']['mandate_status'],
            \urldecode($responseData['responseData']['mandate_text']),
            $responseData['responseData']['creditor_identifier'],
            $responseData['responseData']['iban'],
            $responseData['responseData']['bic']
        );
        /** @var ManagemandateResponse $response */
        $response = pluginApp(ManagemandateResponse::class);

        return $response->init(
            $responseData['success'] ?? false,
            $responseData['errorMessage'] ?? '',
            $responseData['transactionID'] ?? '',
            $mandate
        );
    }
}
