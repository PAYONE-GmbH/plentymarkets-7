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
     * @return AuthResponse
     */
    public static function create(array $responseData)
    {
        /** @var SepaMandate $mandate */
        $mandate = pluginApp(SepaMandate::class);
        $mandate->init(
            $responseData['mandate_identification'],
            $responseData['mandate_status'],
            $responseData['mandate_text'],
            $responseData['creditor_identifier'],
            $responseData['iban'],
            $responseData['bic']
        );
        /** @var AuthResponse $response */
        $response = pluginApp(ManagemandateResponse::class);

        return $response->init(
            $responseData['success'] ?? false,
            $responseData['errorMessage'] ?? '',
            $responseData['transactionID'] ?? ''
        );
    }
}
