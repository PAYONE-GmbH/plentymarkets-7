<?php

namespace Payone\Models;

use Payone\Adapter\SessionStorage;

/**
 * Class BankAccountCache
 */
class BankAccountCache
{
    const STORAGE_KEY = 'PayoneBankAccount';
    /**
     * @var SessionStorage
     */
    private $sessionStorage;

    /**
     * BankAccountCache constructor.
     *
     * @param SessionStorage $sessionStorage
     */
    public function __construct(SessionStorage $sessionStorage)
    {
        $this->sessionStorage = $sessionStorage;
    }

    /**
     * @param BankAccount $bankAccount
     */
    public function storeBankAccount(BankAccount $bankAccount)
    {
        $this->sessionStorage->setSessionValue(
            $this->getStorageKey(),
            $bankAccount
        );
    }

    /**
     * @return BankAccount
     */
    public function loadBankAccount()
    {
        return $this->sessionStorage->getSessionValue($this->getStorageKey());
    }

    public function deleteBankAccount()
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
