<?php

namespace Payone\Models\Api\Clearing;

/**
 * Class ClearingFactory
 */
class ClearingFactory
{
    /**
     * @param array $responseData
     *
     * @return ClearingAbstract
     */
    public static function create(array $responseData)
    {
        if (!($responseData['clearing_bankaccount'] ?? false)) {
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
            $responseData['clearing_bankaccount'] ?? '',
            $responseData['clearing_bankcode'] ?? '',
            $responseData['clearing_bankcountry'] ?? '',
            $responseData['clearing_bankname'] ?? '',
            $responseData['clearing_bankaccountholder'] ?? '',
            $responseData['clearing_bankcity'] ?? '',
            $responseData['clearing_bankiban'] ?? '',
            $responseData['clearing_bankbic'] ?? ''
        );

    }
}
