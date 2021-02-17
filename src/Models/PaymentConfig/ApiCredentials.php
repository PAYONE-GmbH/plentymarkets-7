<?php

namespace Payone\Models\PaymentConfig;

use Payone\Services\SettingsService;

class ApiCredentials
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
     * @param string|null $paymentCode
     * @param int|null $clientId
     * @param int|null $pluginSetId
     * @return string
     */
    public function getKey(string $paymentCode = null, int $clientId = null, int $pluginSetId = null): string
    {
        if ($paymentCode !== null) {
            $key = $this->settingsService->getPaymentSettingsValue('key', $paymentCode, $clientId, $pluginSetId);
            if (!empty($key)) {
                return $key;
            }
        }
        
        return $this->settingsService->getSettingsValue('key', $clientId, $pluginSetId);
    }

    /**
     * @param int|null $clientId
     * @param int|null $pluginSetId
     * @return string
     */
    public function getAid(int $clientId = null, int $pluginSetId = null): string
    {
        return $this->settingsService->getSettingsValue('aid', $clientId, $pluginSetId);
    }

    /**
     * @param int|null $clientId
     * @param int|null $pluginSetId
     * @return string
     */
    public function getMid(int $clientId = null, int $pluginSetId = null): string
    {
        return $this->settingsService->getSettingsValue('mid', $clientId, $pluginSetId);
    }

    /**
     * @param string|null $paymentCode
     * @param int|null $clientId
     * @param int|null $pluginSetId
     * @return string
     */
    public function getPortalid(string $paymentCode = null, int $clientId = null, int $pluginSetId = null): string
    {
        if ($paymentCode !== null) {
            $portalId = $this->settingsService->getPaymentSettingsValue('portalId', $paymentCode, $clientId, $pluginSetId);
            if (!empty($portalId)) {
                return $portalId;
            }
        }
        
        return $this->settingsService->getSettingsValue('portalId');
    }

    /**
     * @param int|null $clientId
     * @param int|null $pluginSetId
     * @return string
     */
    public function getMode(int $clientId = null, int $pluginSetId = null): string
    {
        $mode = $this->settingsService->getSettingsValue('mode', $clientId, $pluginSetId);
        return ($mode == 1) ? 'live' : 'test';
    }

    /**
     * @param string|null $paymentCode
     * @param int|null $clientId
     * @param int|null $pluginSetId
     * @return array
     */
    public function getApiCredentials(string $paymentCode = null, int $clientId = null, int $pluginSetId = null): array
    {
        $apiContextParams = [];
        $apiContextParams['aid'] = $this->getAid($clientId, $pluginSetId);
        $apiContextParams['mid'] = $this->getMid($clientId, $pluginSetId);
        $apiContextParams['portalid'] = $this->getPortalid($paymentCode, $clientId, $pluginSetId);
        $apiContextParams['key'] = $this->getKey($paymentCode, $clientId, $pluginSetId);
        $apiContextParams['mode'] = $this->getMode($clientId, $pluginSetId);

        return $apiContextParams;
    }
}
