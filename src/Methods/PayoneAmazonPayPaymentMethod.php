<?php

namespace Payone\Methods;

class PayoneAmazonPayPaymentMethod extends PaymentAbstract
{
    const PAYMENT_CODE = 'PAYONE_PAYONE_AMAZON_PAY';

    const CLEARING_TYPE = "wlt";
    const WALLET_TYPE = "AMZ";

    /**
     * Check if it is allowed to switch from this payment method
     * @return bool
     */
    public function isSwitchableFrom($orderId = null): bool
    {
        return true;
    }
}
