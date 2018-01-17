<?php

namespace Payone\Models;

use Payone\Adapter\SessionStorage;

class CreditCardCheckResponseRepository
{
    const STORAGE_KEY = 'payone_cc_check_response';
    /**
     * @var SessionStorage
     */
    private $sessionStorage;

    /**
     * PaymentCache constructor.
     *
     * @param SessionStorage $sessionStorage
     */
    public function __construct(SessionStorage $sessionStorage)
    {
        $this->sessionStorage = $sessionStorage;
    }

    /**
     * Getter for SessionStorage
     *
     * @return SessionStorage
     */
    public function getSessionStorage(): SessionStorage
    {
        return $this->sessionStorage;
    }

    /**
     * @param CreditCardCheckResponse $cardCheckResponse
     */
    public function storeLastResponse(CreditCardCheckResponse $cardCheckResponse)
    {
        $this->sessionStorage->setSessionValue(self::STORAGE_KEY, $cardCheckResponse);
    }

    /**
     * @return CreditCardCheckResponse
     */
    public function loadLastResponse()
    {
        return $this->sessionStorage->getSessionValue(self::STORAGE_KEY);
    }
}
