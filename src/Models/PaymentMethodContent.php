<?php

namespace Payone\Models;

use Payone\Methods\PayoneCCPaymentMethod;
use Payone\Methods\PayoneDirectDebitPaymentMethod;
use Plenty\Modules\Payment\Events\Checkout\GetPaymentMethodContent;

/**
 * Class PaymentMethodContent
 */
class PaymentMethodContent
{
    /**
     * @param string $paymentCode
     *
     * @return string
     */
    public function getPaymentContentType($paymentCode)
    {
        switch ($paymentCode) {
            case 'none':
            case PayoneDirectDebitPaymentMethod::PAYMENT_CODE:
            case PayoneCCPaymentMethod::PAYMENT_CODE:
                return GetPaymentMethodContent::RETURN_TYPE_HTML;
        }

        return GetPaymentMethodContent::RETURN_TYPE_CONTINUE;
    }
}
