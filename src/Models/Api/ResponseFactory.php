<?php

namespace Payone\Models\Api;

/**
 * Class ResponseFactory
 */
class ResponseFactory
{
    /**
     * @param array $responseData
     *
     * @return Response
     */
    public static function create(string $transactionType, array $responseData)
    {
        $genericResponse = self::createGenericResponse($responseData);

        return $genericResponse;
    }

    /**
     * @param array $responseData
     *
     * @return $this
     */
    private static function createGenericResponse(array $responseData): Response
    {
        /** @var Response $response */
        $response = pluginApp(Response::class);

        $success = isset($responseData['success']) ? (bool) $responseData['success'] : false;
        $errorMessage = isset($responseData['errorMessage']) ? (string) $responseData['errorMessage'] : '';
        $transactionID = isset($responseData['transactionID']) ? (string) $responseData['transactionID'] : '';

        return $response->init($success, $errorMessage, $transactionID);
    }
}
