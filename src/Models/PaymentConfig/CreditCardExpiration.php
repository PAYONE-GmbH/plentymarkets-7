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
     * @return int
     */
    public function getMinExpireTimeInDays()
    {
        return (int) $this->configRepo->get(PayoneCCPaymentMethod::PAYMENT_CODE . '.minExpireTime');
    }
}
