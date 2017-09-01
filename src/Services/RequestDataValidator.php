<?php

namespace Payone\Services;

class RequestDataValidator
{
    /**
     * @param array $data
     */
    public function validate(array $data)
    {
        $paymentCode = $data['paymentCode'];
        foreach (['aid', 'mid', 'portalid', 'key'] as $key) {
            if (!$data['context'][$key]) {
                throw new \Exception('Check your payment config for ' . $paymentCode . '. Missing value for "' . $key . '"');
            }
        }
    }
}
