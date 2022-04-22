<?php

namespace Payone\Helpers;


use Plenty\Modules\Frontend\Session\Storage\Contracts\FrontendSessionStorageFactoryContract;
use Plenty\Modules\Frontend\Session\Storage\Models\LocaleSettings;
use Plenty\Modules\Helper\Services\WebstoreHelper;
use Plenty\Modules\System\Models\WebstoreConfiguration;
use Plenty\Modules\Webshop\Helpers\UrlQuery;

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
    protected $sessionStorage;

    /**
     * @var LocaleSettings
     */
    protected $localeSettings;

    /**
     * @var WebstoreHelper
     */
    protected $webstoreHelper;

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
    public function getPlentyDomain(): string
    {
        /** @var WebstoreHelper $webstoreHelper */
        $webstoreHelper = pluginApp(WebstoreHelper::class);
        /** @var WebstoreConfiguration $webstoreConfig */
        $webstoreConfig = $webstoreHelper->getCurrentWebstoreConfiguration();

        return $webstoreConfig->domainSsl;
    }

    /**
     * @return string
     */
    public function getCurrentLanguage(): string
    {
        $config = $this->localeSettings->toArray();
        return $config['language'] ?? $this->getDefaultLanguage();
    }

    /**
     * @return string
     */
    public function getCurrentCurrency(): string
    {
        $config = $this->localeSettings->toArray();
        return $config['currency'] ?? $this->getDefaultCurrency();
    }

    /**
     * @return string
     */
    public function getIpAddress(): string
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
    public function getCurrentLocale(): string
    {
        return strtolower($this->getCurrentLanguage()) . '-' . strtoupper($this->getCurrentLanguage());
    }

    public static function getTrailingSlash()
    {
        if(UrlQuery::shouldAppendTrailingSlash()) {
            return '/';
        }
        return '';
    }

    /**
     * Ensures an ip address is both a valid IP and does not fall within
     * a private network range.
     *
     * @param string $ip
     * @return bool
     */
    protected function isIpValid(string $ip): bool
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
    protected function getDefaultLanguage(): string
    {
        $config = $this->webstoreHelper->getCurrentWebstoreConfiguration()->toArray();
        return $config['defaultLanguage'] ?? self::DEFAULT_LANGUAGE;
    }

    /**
     * @return string
     */
    protected function getDefaultCurrency(): string
    {
        $config = $this->webstoreHelper->getCurrentWebstoreConfiguration()->toArray();
        return $config['defaultCurrency'] ?? self::DEFAULT_CURRENCY;
    }
}
