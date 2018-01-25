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
     * @return ResponseAbstract|Response
     */
    public static function create(array $responseData)
    {
        /** @var Response $response */
        $response = pluginApp(Response::class);

        $success = $responseData['success'] ?? false;
        $errorMessage = $responseData['errorMessage'] ?? '';
        $transactionID = $responseData['transactionID'] ?? '';

        return $response->init($success, $errorMessage, $transactionID);
    }
}
