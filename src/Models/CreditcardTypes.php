<?php

namespace Payone\Models;

use Payone\Methods\PayoneCCPaymentMethod;
use Payone\Services\SettingsService;

class CreditcardTypes
{
    const VISA = 'V';
    const MASTERCARD = 'M';
    const AMEX = 'A';
    const MAESTRO_INT = 'O';
    const MAESTRO_UK = 'U';
    const DINERS = 'D';
    const CARTE_BLEUE = 'B';
    const DISCOVER = 'C';
    const JCB = 'J';
    const CHINA_UNION_PAY = 'P';

    /**
     * @var SettingsService
     */
    protected $settingsService;

    /**
     * Api constructor.
     *
     * @param SettingsService $settingsService
     */
    public function __construct(SettingsService $settingsService)
    {
        $this->settingsService = $settingsService;
    }

    /**
     * @return array
     */
    public function getAllowedTypes(): array
    {
        $allowedTypesFromConfig = $this->settingsService->getPaymentSettingsValue('AllowedCardTypes',PayoneCCPaymentMethod::PAYMENT_CODE);
        if(!is_array($allowedTypesFromConfig)) {
            return $allowedTypesFromConfig;
        }
        return [];
    }

    /**
     * @return string[]
     */
    public function getCreditCardTypes(): array
    {
        return [
            self::VISA,
            self::MASTERCARD,
            self::AMEX,
            self::MAESTRO_INT,
            self::MAESTRO_UK,
            self::DINERS,
            self::CARTE_BLEUE,
            self::DISCOVER,
            self::JCB,
            self::CHINA_UNION_PAY,
        ];
    }
}
