<?php

// strict

namespace Payone\Methods;

/**
 * Class PayoneInvoiceSecurePaymentMethod
 */
class PayoneInvoiceSecurePaymentMethod extends PaymentAbstract
{
    const PAYMENT_CODE = 'PAYONE_PAYONE_INVOICE_SECURE';

    /**
     * Can the delivery address be different from the invoice address?
     *
     * @return bool
     */
    public function canHandleDifferingDeliveryAddress(): bool
    {
        return false;
    }
}
