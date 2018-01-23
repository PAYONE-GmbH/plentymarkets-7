<?php

namespace Payone\Models;

use Payone\Adapter\Config as ConfigAdapter;
use Payone\Methods\PayoneCCPaymentMethod;

class CreditCardStyle
{
    /** @var ConfigAdapter */
    private $configRepo;

    /**
     * Api constructor.
     *
     * @param $paymentCode
     * @param ConfigAdapter $configRepo
     */
    public function __construct(
        ConfigAdapter $configRepo
    ) {
        $this->configRepo = $configRepo;
    }

    /**
     * @return float
     */
    public function getDefaultWidthInPx()
    {
        return (float) $this->configRepo->get(PayoneCCPaymentMethod::PAYMENT_CODE . '.defaultWidthInPx');
    }

    /**
     * @return float
     */
    public function getDefaultHeightInPx()
    {
        return (float) $this->configRepo->get(PayoneCCPaymentMethod::PAYMENT_CODE . '.defaultHeightInPx');
    }

    /**
     * @return string
     */
    public function getDefaultStyle(): string
    {
        return $this->configRepo->get(PayoneCCPaymentMethod::PAYMENT_CODE . '.defaultStyle');
    }
}
