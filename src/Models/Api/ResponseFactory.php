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
     * @param array $responseData
     *
     * @return Response
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
                isset($responseData['success']) ?? false,
                isset($responseData['errorMessage']) ?? '',
                isset($responseData['transactionID']) ?? '',
                $clearing
            );
        }

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

        $success = isset($responseData['success']) ?? false;
        $errorMessage = isset($responseData['errorMessage']) ?? '';
        $transactionID = isset($responseData['transactionID']) ?? '';

        return $response->init($success, $errorMessage, $transactionID);
    }
}
