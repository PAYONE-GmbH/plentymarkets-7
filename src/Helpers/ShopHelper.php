<?php

//strict

namespace Payone\Helpers;

use Payone\PluginConstants;
use Plenty\Modules\Frontend\Session\Storage\Contracts\FrontendSessionStorageFactoryContract;
use Plenty\Modules\Frontend\Session\Storage\Models\LocaleSettings;
use Plenty\Modules\Helper\Services\WebstoreHelper;
use Plenty\Plugin\ConfigRepository;

/**
 * Class ShopHelper
 */
class ShopHelper
{
    const DEFAULT_LANGUAGE = 'de';
    const DEFAULT_CURRENCY = 'EUR';
    /**
     * @var FrontendSessionStorageFactoryContract
     */
    private $sessionStorage;
    /**
     * @var LocaleSettings
     */
    private $localeSettings;
    /**
     * @var WebstoreHelper
     */
    private $webstoreHelper;

    /**
     * @param FrontendSessionStorageFactoryContract $sessionStorage
     * @param LocaleSettings $localeSettings
     * @param WebstoreHelper $webstoreHelper
     */
    public function __construct(
        FrontendSessionStorageFactoryContract $sessionStorage,
        LocaleSettings $localeSettings,
        WebstoreHelper $webstoreHelper
    ) {
        $this->sessionStorage = $sessionStorage;
        $this->localeSettings = $localeSettings;
        $this->webstoreHelper = $webstoreHelper;
    }

    /**
     * @return string
     */
    public function getPlentyDomain()
    {
        /** @var \Plenty\Modules\Helper\Services\WebstoreHelper $webstoreHelper */
        $webstoreHelper = pluginApp(\Plenty\Modules\Helper\Services\WebstoreHelper::class);

        /** @var \Plenty\Modules\System\Models\WebstoreConfiguration $webstoreConfig */
        $webstoreConfig = $webstoreHelper->getCurrentWebstoreConfiguration();

        return $webstoreConfig->domainSsl;
    }

    /**
     * @return string
     */
    public function getCurrentLanguage()
    {
        $config = $this->localeSettings->toArray();

        return $config['language'] ?? $this->getDefaultLanguage();
    }

    /**
     * @return string
     */
    public function getCurrentCurrency()
    {
        $config = $this->localeSettings->toArray();

        return $config['currency'] ?? $this->getDefaultCurrency();
    }

    /**
     * @return bool
     */
    public function isDebugModeActive()
    {
        /** @var ConfigRepository $config */
        $config = pluginApp(ConfigRepository::class);

        return (bool) $config->get(PluginConstants::NAME . '.debugging.active');
    }

    /**
     * @return string
     */
    public function getIpAddress()
    {
        $ipKeys = [
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR',
        ];

        foreach ($ipKeys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);
                    if ($this->isIpValid($ip)) {
                        return $ip;
                    }
                }
            }
        }

        return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
    }

    /**
     * @return string
     */
    public function getCurrentLocale()
    {
        return strtolower($this->getCurrentLanguage()) . '-' . strtoupper($this->getCurrentLanguage());
    }

    /**
     * Ensures an ip address is both a valid IP and does not fall within
     * a private network range.
     *
     * @param $ip
     *
     * @return bool
     */
    private function isIpValid($ip)
    {
        if (
            filter_var(
                $ip,
                FILTER_VALIDATE_IP,
                FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false
        ) {
            return false;
        }

        return true;
    }

    /**
     * @return string
     */
    private function getDefaultLanguage(): string
    {
        $config = $this->webstoreHelper->getCurrentWebstoreConfiguration()->toArray();

        return $config['defaultLanguage'] ?? self::DEFAULT_LANGUAGE;
    }

    /**
     * @return string
     */
    private function getDefaultCurrency(): string
    {
        $config = $this->webstoreHelper->getCurrentWebstoreConfiguration()->toArray();

        return $config['defaultCurrency'] ?? self::DEFAULT_CURRENCY;
    }
}
