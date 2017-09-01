<?php

namespace Payone\Models;

use Payone\Adapter\SessionStorage;
use Payone\PluginConstants;
use Plenty\Modules\Payment\Models\Payment;

class PaymentCache
{
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
     * @param string $paymentCode
     * @param Payment $payment
     */
    public function storePayment(string $paymentCode, Payment $payment)
    {
        $this->sessionStorage->setSessionValue(
            $this->getStorageKey($paymentCode),
            $payment
        );
    }

    /**
     * @param string $paymentCode
     *
     * @return Payment
     */
    public function loadPayment(string $paymentCode)
    {
        return $this->sessionStorage->getSessionValue($this->getStorageKey($paymentCode));
    }

    /**
     * @param string $paymentCode
     */
    public function deletePayment(string $paymentCode)
    {
        $this->sessionStorage->setSessionValue(
            $this->getStorageKey($paymentCode),
            null
        );
    }

    /**
     * @param string $paymentCode
     *
     * @return string
     */
    private function getStorageKey(string $paymentCode): string
    {
        return PluginConstants::NAME . '_mopid_' . $paymentCode;
    }
}
