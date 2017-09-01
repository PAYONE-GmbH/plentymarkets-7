<?php

namespace Payone\Models;

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
                return GetPaymentMethodContent::RETURN_TYPE_HTML;
        }

        return GetPaymentMethodContent::RETURN_TYPE_CONTINUE;
    }
}
