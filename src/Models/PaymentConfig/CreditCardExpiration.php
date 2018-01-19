<?php

namespace Payone\Models\PaymentConfig;

use Payone\Adapter\Config as ConfigAdapter;
use Payone\Methods\PayoneCCPaymentMethod;

class CreditCardExpiration
{
    /** @var ConfigAdapter */
    private $configRepo;

    /**
     * Api constructor.
     *
     * @param $paymentCode
     * @param ConfigAdapter $configRepo
     */
    public function __construct(ConfigAdapter $configRepo)
    {
        $this->configRepo = $configRepo;
    }

    /**
     * @return string
     */
    public function getMinExpireTimeInDays(): string
    {
        return (int) $this->configRepo->get(PayoneCCPaymentMethod::PAYMENT_CODE . '.minExpireTime');
    }
}
