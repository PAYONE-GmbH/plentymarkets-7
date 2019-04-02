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
     * @return string
     */
    public function getKey()
    {
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
     * @return string
     */
    public function getPortalid()
    {
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
     * @return array
     */
    public function getApiCredentials()
    {
        $apiContextParams = [];
        $apiContextParams['aid'] = $this->getAid();
        $apiContextParams['mid'] = $this->getMid();
        $apiContextParams['portalid'] = $this->getPortalid();
        $apiContextParams['key'] = $this->getKey();
        $apiContextParams['mode'] = $this->getMode();

        return $apiContextParams;
    }
}
