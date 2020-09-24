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
     * @return mixed
     */
    public static function create(string $actionType, array $responseData)
    {
        switch ($actionType) {
            case GenericPayment::ACTIONTYPE_GETCONFIGURATION:
                return self::makeGetConfigurationResponse($responseData);
            case GenericPayment::ACTIONTYPE_GETORDERREFERENCEDETAILS:
                return self::makeGetOrderReferenceDetailsResponse($responseData);
            case GenericPayment::ACTIONTYPE_SETORDERREFERENCEDETAILS:
                return self::makeSetOrderReferenceDetailsResponse($responseData);
            case GenericPayment::ACTIONTYPE_CONFIRMORDERREFERENCE:
                return self::makeConfirmOrderReferenceResponse($responseData);
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

        if(!$responseData['success']) {
            return $response->init(
                false,
                $responseData['responseData']['responseData']['customermessage']
            );
        }

        return $response->init(
            $responseData['success'] ?? false,
            $responseData['errormessage'] ?? '',
            $responseData['responseData']['add_paydata[client_id]'] ?? '',
            $responseData['responseData']['add_paydata[seller_id]'] ?? '',
            $responseData['responseData']['currency'] ?? '',
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

        if(!$responseData['success']) {
            return $response->init(
                false,
                $responseData['responseData']['responseData']['customermessage']
            );
        }

        return $response->init(
            $responseData['success'] ?? false,
            $responseData['errormessage'] ?? '',
            $responseData['responseData']['add_paydata[email]'] ?? '',
            $responseData['responseData']['add_paydata[shipping_zip]'] ?? '',
            $responseData['responseData']['add_paydata[shipping_street]'] ?? '',
            $responseData['responseData']['add_paydata[shipping_company]'] ?? '',
            $responseData['responseData']['add_paydata[shipping_city]'] ?? '',
            $responseData['responseData']['add_paydata[shipping_type]'] ?? '',
            $responseData['responseData']['add_paydata[shipping_country]'] ?? '',
            $responseData['responseData']['add_paydata[shipping_district]'] ?? '',
            $responseData['responseData']['add_paydata[shipping_telephonenumber]'] ?? '',
            $responseData['responseData']['add_paydata[shipping_state]'] ?? '',
            $responseData['responseData']['add_paydata[shipping_firstname]'] ?? '',
            $responseData['responseData']['add_paydata[shipping_lastname]'] ?? '',
            $responseData['responseData']['add_paydata[billing_zip]'] ?? '',
            $responseData['responseData']['add_paydata[billing_street]'] ?? '',
            $responseData['responseData']['add_paydata[billing_company]'] ?? '',
            $responseData['responseData']['add_paydata[billing_city]'] ?? '',
            $responseData['responseData']['add_paydata[billing_type]'] ?? '',
            $responseData['responseData']['add_paydata[billing_country]'] ?? '',
            $responseData['responseData']['add_paydata[billing_firstname]'] ?? '',
            $responseData['responseData']['add_paydata[billing_lastname]'] ?? '',
            $responseData['responseData']['add_paydata[billing_district]'] ?? '',
            $responseData['responseData']['add_paydata[billing_telephonenumber]'] ?? '',
            $responseData['responseData']['add_paydata[billing_state]'] ?? '',
            $responseData['responseData']['add_paydata[storename]'] ?? '',
            $responseData['responseData']['workorderid'] ?? ''
        );
    }

    /**
     * @param array $responseData
     * @return SetOrderReferenceDetailsResponse
     */
    private static function makeSetOrderReferenceDetailsResponse(array $responseData): SetOrderReferenceDetailsResponse
    {
        /** @var SetOrderReferenceDetailsResponse $response */
        $response = pluginApp(SetOrderReferenceDetailsResponse::class);

        if(!$responseData['success']) {
            return $response->init(
                false,
                $responseData['responseData']['responseData']['customermessage']
            );
        }

        return $response->init(
            $responseData['success'] ?? false,
            $responseData['errormessage'] ?? '',
            $responseData['responseData']['add_paydata[amazonAddressToken]'] ?? '',
            $responseData['responseData']['add_paydata[amazonReferenceId]'] ?? '',
            $responseData['responseData']['add_paydata[storename]'] ?? '',
            $responseData['responseData']['amount'] ?? '',
            $responseData['responseData']['currency'] ?? '',
            $responseData['responseData']['workorderid'] ?? ''
        );
    }

    /**
     * @param array $responseData
     * @return ConfirmOrderReferenceResponse
     */
    private static function makeConfirmOrderReferenceResponse(array $responseData)
    {
        /** @var ConfirmOrderReferenceResponse $response */
        $response = pluginApp(ConfirmOrderReferenceResponse::class);

        if(!$responseData['success']) {
            return $response->init(
                false,
                $responseData['responseData']['responseData']['customermessage']
            );
        }

        return $response->init(
            $responseData['success'] ?? false,
            $responseData['errormessage'] ?? '',
            $responseData['responseData']['workorderid'] ?? ''
        );
    }
}
