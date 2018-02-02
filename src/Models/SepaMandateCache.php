<?php

namespace Payone\Models;

use Payone\Adapter\SessionStorage;

class SepaMandateCache
{
    const STORAGE_KEY = 'PayoneSepaMandate';
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
     * @param SepaMandate $sepaMandate
     */
    public function store(SepaMandate $sepaMandate)
    {
        $this->sessionStorage->setSessionValue(
            $this->getStorageKey(),
            $sepaMandate
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
     * @return string
     */
    private function getStorageKey(): string
    {
        return self::STORAGE_KEY;
    }
}
