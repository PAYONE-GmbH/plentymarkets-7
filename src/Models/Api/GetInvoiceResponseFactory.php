<?php

namespace Payone\Models\Api;

/**
 * Class GetInvoiceResponseFactory
 */
class GetInvoiceResponseFactory
{
    /**
     * @param array $responseData
     *
     * @return GetInvoiceResponse
     */
    public static function create(array $responseData)
    {
        /** @var GetInvoiceResponse $response */
        $response = pluginApp(GetInvoiceResponse::class);

        return $response->init(
            $responseData['success'] ?? false,
            $responseData['errormessage'] ?? '',
            $responseData['responseData']['document'] ?? ''
        );
    }
}
