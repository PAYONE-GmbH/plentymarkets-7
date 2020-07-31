<?php

namespace Payone\Models\PaymentConfig;

use Payone\Adapter\Config as ConfigAdapter;

class ApiCredentials
{
    /** @var ConfigAdapter */
    private $configRepo;

    /**
     * Api constructor.
     *
     * @param $paymentCode
     * @param ConfigAdapter $configRepo
     */
    public function __construct(
        ConfigAdapter $configRepo
    ) {
        $this->configRepo = $configRepo;
    }

    /**
     * @param int|null $paymentCode
     * @return string
     */
    public function getKey($paymentCode = null)
    {
        if ($paymentCode !== null) {
            $key = $this->configRepo->get($paymentCode . '.key');
            if (!empty($key)) {
                return $key;
            }
        }
        
        return $this->configRepo->get('key');
    }

    /**
     * @return string
     */
    public function getAid()
    {
        return $this->configRepo->get('aid');
    }

    /**
     * @return string
     */
    public function getMid()
    {
        return $this->configRepo->get('mid');
    }

    /**
     * @param int|null $paymentCode
     * @return string
     */
    public function getPortalid($paymentCode = null)
    {
        if ($paymentCode !== null) {
            $portalId = $this->configRepo->get($paymentCode . '.portalid');
            if (!empty($portalId)) {
                return $portalId;
            }
        }
        
        return $this->configRepo->get('portalid');
    }

    /**
     * @return string
     */
    public function getMode()
    {
        $mode = $this->configRepo->get('mode');

        return ($mode == 1) ? 'live' : 'test';
    }

    /**
     * @param int|null $paymentCode
     * @return array
     */
    public function getApiCredentials($paymentCode = null)
    {
        $apiContextParams = [];
        $apiContextParams['aid'] = $this->getAid();
        $apiContextParams['mid'] = $this->getMid();
        $apiContextParams['portalid'] = $this->getPortalid($paymentCode);
        $apiContextParams['key'] = $this->getKey($paymentCode);
        $apiContextParams['mode'] = $this->getMode();

        return $apiContextParams;
    }
}
