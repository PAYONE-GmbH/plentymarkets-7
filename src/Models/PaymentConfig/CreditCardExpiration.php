<?php

namespace Payone\Models\PaymentConfig;

use Payone\Methods\PayoneCCPaymentMethod;
use Payone\Services\SettingsService;

class CreditCardExpiration
{
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
     * @return int
     */
    public function getMinExpireTimeInDays(): int
    {
        return (int) $this->settingsService->getPaymentSettingsValue('minExpireTime', PayoneCCPaymentMethod::PAYMENT_CODE);
    }
}
