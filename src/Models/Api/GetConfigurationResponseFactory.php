<?php

namespace Payone\Models\Api;

/**
 * Class GetConfigurationResponseFactory
 */
class GetConfigurationResponseFactory
{
    /**
     * @param array $responseData
     *
     * @return GetConfigurationResponse
     */
    public static function create(array $responseData): GetConfigurationResponse
    {
        /** @var GetConfigurationResponse $response */
        $response = pluginApp(GetConfigurationResponse::class);

        return $response->init(
            $responseData['success'] ?? false,
            $responseData['errormessage'] ?? '',
            $responseData['responseData']['add_paydata[client_id]'] ?? '',
            $responseData['responseData']['add_paydata[seller_id]'] ?? '',
            $responseData['responseData']['workorderid'] ?? ''
        );
    }
}
