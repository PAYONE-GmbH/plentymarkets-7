<?php

namespace Payone\Models;

use Payone\Adapter\SessionStorage;

class SepaMandateCache
{
    const PAYONE_SEPA_MANDATE = 'PayoneSepaMandate';
    /**
     * @var SessionStorage
     */
    private $sessionStorage;

    /**
     * SepaMandateCache constructor.
     *
     * @param SessionStorage $sessionStorage
     */
    public function __construct(SessionStorage $sessionStorage)
    {
        $this->sessionStorage = $sessionStorage;
    }

    /**
     * @param string $SepaMandateCode
     * @param SepaMandate $SepaMandate
     */
    public function store(SepaMandate $SepaMandate)
    {
        $this->sessionStorage->setSessionValue(
            $this->getStorageKey(),
            $SepaMandate
        );
    }

    /**
     *
     * @return SepaMandate|''
     */
    public function load()
    {
        return $this->sessionStorage->getSessionValue($this->getStorageKey());
    }


    public function delete()
    {
        $this->sessionStorage->setSessionValue(
            $this->getStorageKey(),
            null
        );
    }

    /**
     *
     * @return string
     */
    private function getStorageKey(): string
    {
        return self::PAYONE_SEPA_MANDATE;
    }
}
