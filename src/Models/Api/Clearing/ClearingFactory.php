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
        if (!($responseData['clearing'] ?? false)) {
            return pluginApp(EmptyClearing::class);
        }

        return self::createBankClearing($responseData['clearing']);
    }

    /**
     * @param object $clearing
     *
     * @return ClearingAbstract
     */
    private static function createBankClearing($clearing): ClearingAbstract
    {
        /** @var Bank $response */
        $response = pluginApp(Bank::class);

        return $response->init(
            $clearing['bankaccount'] ?? '',
            $clearing['bankcode'] ?? '',
            $clearing['bankcountry'] ?? '',
            $clearing['bankname'] ?? '',
            $clearing['bankaccountholder'] ?? '',
            $clearing['bankcity'] ?? '',
            $clearing['bankiban'] ?? '',
            $clearing['bankbic'] ?? ''
        );
    }
}
