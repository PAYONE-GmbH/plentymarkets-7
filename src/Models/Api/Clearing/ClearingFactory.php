<?php

namespace Payone\Models\Api\Clearing;

/**
 * Class ClearingFactory
 */
class ClearingFactory
{
    /**
     * @param array|null $responseData
     *
     * @return ClearingAbstract
     */
    public static function create($responseData)
    {
        if (!($responseData['bankaccount'] ?? false)) {
            return pluginApp(EmptyClearing::class);
        }

        return self::createBankClearing($responseData);
    }

    /**
     * @param array $responseData
     *
     * @return $this
     */
    private static function createBankClearing(array $responseData): ClearingAbstract
    {
        /** @var Bank $response */
        $response = pluginApp(Bank::class);

        return $response->init(
            $responseData['bankaccount'] ?? '',
            $responseData['bankcode'] ?? '',
            $responseData['bankcountry'] ?? '',
            $responseData['bankname'] ?? '',
            $responseData['bankaccountholder'] ?? '',
            $responseData['bankcity'] ?? '',
            $responseData['bankiban'] ?? '',
            $responseData['bankbic'] ?? ''
        );
    }
}
