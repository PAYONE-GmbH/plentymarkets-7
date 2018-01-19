<?php

namespace Payone\Services;

use Payone\Models\PaymentConfig\CreditCardExpiration;
use Payone\Validator\CardExpireDate;

class RequestDataValidator
{
    /**
     * @var CreditCardExpiration
     */
    private $creditCardExpiration;

    /**
     * RequestDataValidator constructor.
     */
    public function __construct(CardExpireDate $creditCardExpiration)
    {
        $this->creditCardExpiration = $creditCardExpiration;
    }

    /**
     * TODO: refactor using composite pattern
     *
     * @param array $data
     */
    public function validate(array $data)
    {
        $paymentCode = $data['ccCheck'];
        if (isset($data['ccCheck']['expiredate'])) {
            $this->creditCardExpiration->validate(\DateTime::createFromFormat('Y-m-d', $data['ccCheck']['expiredate']));
        }

        foreach (['aid', 'mid', 'portalid', 'key'] as $key) {
            if (!$data['context'][$key]) {
                throw new \Exception('Check your payment config for ' . $paymentCode . '. Missing value for "' . $key . '"');
            }
        }
    }
}
