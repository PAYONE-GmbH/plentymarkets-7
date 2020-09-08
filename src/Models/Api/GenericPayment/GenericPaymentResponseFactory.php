<?php

namespace Payone\Models\Api\GenericPayment;

use Payone\Providers\Api\Request\Models\GenericPayment;

/**
 * Class GetConfigurationResponseFactory
 */
class GenericPaymentResponseFactory
{
    /**
     * @param string $actionType
     * @param array $responseData
     * @return GetConfigurationResponse|GetOrderReferenceDetailsResponse
     */
    public static function create(string $actionType, array $responseData)
    {
        switch($actionType)
        {
            case GenericPayment::ACTIONTYPE_GETCONFIGURATION:
                return self::makeGetConfigurationResponse($responseData);
                break;
            case GenericPayment::ACTIONTYPE_GETORDERREFERENCEDETAILS:
                return self::makeGetOrderReferenceDetailsResponse($responseData);
                break;
        }

    }

    /**
     * @param array $responseData
     * @return GetConfigurationResponse
     */
    private static function makeGetConfigurationResponse(array $responseData): GetConfigurationResponse
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

    /**
     * @param array $responseData
     * @return GetOrderReferenceDetailsResponse
     */
    private static function makeGetOrderReferenceDetailsResponse(array $responseData): GetOrderReferenceDetailsResponse
    {
        /** @var GetOrderReferenceDetailsResponse $response */
        $response = pluginApp(GetOrderReferenceDetailsResponse::class);

        return $response->init(
            $responseData['success'] ?? false,
            $responseData['errormessage'] ?? '',
            $responseData['responseData']['add_paydata[client_id]'] ?? '',
            $responseData['responseData']['add_paydata[seller_id]'] ?? '',
            $responseData['responseData']['workorderid'] ?? ''
        );
    }
}
