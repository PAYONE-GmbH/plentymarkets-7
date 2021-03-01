<?php

namespace Payone\Models;

use Payone\Methods\PayoneCCPaymentMethod;
use Payone\Services\SettingsService;

class CreditCardStyle
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
     * @return float
     */
    public function getDefaultWidthInPx()
    {
        return (float)$this->settingsService->getPaymentSettingsValue('defaultWidthInPx',PayoneCCPaymentMethod::PAYMENT_CODE);
    }

    /**
     * @return float
     */
    public function getDefaultHeightInPx()
    {
        return (float)$this->settingsService->getPaymentSettingsValue('defaultHeightInPx',PayoneCCPaymentMethod::PAYMENT_CODE);
    }

    /**
     * @return string
     */
    public function getDefaultStyle(): string
    {
        return (string)$this->settingsService->getPaymentSettingsValue('defaultStyle',PayoneCCPaymentMethod::PAYMENT_CODE);
    }

}
