<?php

namespace Payone\Models\Api;

use Payone\Models\Api\Clearing\ClearingFactory;
use Payone\Services\Api;

/**
 * Class ResponseFactory
 */
class ResponseFactory
{
    /**
     * @param string $transactionType
     * @param array $responseData
     * @return ResponseAbstract|Response|AuthResponse
     */
    public static function create(string $transactionType, array $responseData)
    {
        $genericResponse = self::createGenericResponse($responseData);

        if (
        in_array(
            $transactionType,
            [
                Api::REQUEST_TYPE_PRE_AUTH,
                Api::REQUEST_TYPE_AUTH,
            ]
        )
        ) {
            $clearing = ClearingFactory::create($responseData['responseData']);
            /** @var AuthResponse $response */
            $response = pluginApp(AuthResponse::class);

            return $response->init(
                $responseData['success'] ?? false,
                $responseData['errorMessage'] ?? '',
                $responseData['transactionID'] ?? '',
                $clearing
            );
        }

        return $genericResponse;
    }

    /**
     * @param array $responseData
     * @return Response
     */
    private static function createGenericResponse(array $responseData): Response
    {
        /** @var Response $response */
        $response = pluginApp(Response::class);

        $success = $responseData['success'] ?? false;
        $errorMessage = $responseData['errorMessage'] ?? '';
        $transactionID = $responseData['transactionID'] ?? '';

        return $response->init($success, $errorMessage, $transactionID);
    }
}
