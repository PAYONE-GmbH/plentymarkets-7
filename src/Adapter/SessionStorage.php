<?php

//strict

namespace Payone\Adapter;

use Plenty\Modules\Frontend\Session\Storage\Contracts\FrontendSessionStorageFactoryContract;

/**
 * Class SessionStorage
 */
class SessionStorage
{
    /**
     * @var FrontendSessionStorageFactoryContract
     */
    private $sessionStorage;
    /**
     * @var Logger
     */
    private $logger;

    /**
     * SessionStorageService constructor.
     *
     * @param FrontendSessionStorageFactoryContract $sessionStorage
     * @param Logger $logger
     */
    public function __construct(FrontendSessionStorageFactoryContract $sessionStorage, Logger $logger)
    {
        $this->sessionStorage = $sessionStorage;
        $this->logger = $logger;
    }

    /**
     * Set the session value
     *
     * @param string $name
     * @param $value
     */
    public function setSessionValue(string $name, $value)
    {
        $this->logger->setIdentifier(__METHOD__)->debug(
            'Session.setSessionValue',
            ['name' => $name, 'value' => $value]
        );
        $this->sessionStorage->getPlugin()->setValue($name, $value);
    }

    /**
     * Get the session value
     *
     * @param string $name
     *
     * @return mixed
     */
    public function getSessionValue(string $name)
    {
        $value = $this->sessionStorage->getPlugin()->getValue($name);
        $this->logger->setIdentifier(__METHOD__)->debug(
            'Session.getSessionValue',
            ['name' => $name, 'value' => $value]
        );

        return $value;
    }
}
