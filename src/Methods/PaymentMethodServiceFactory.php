<?php

namespace Payone\Methods;

/**
 * Class PaymentMethodServiceFactory
 */
class PaymentMethodServiceFactory
{
    /**
     * @param $paymentCode
     *
     * @return PaymentAbstract
     */
    public static function create($paymentCode)
    {
        switch ($paymentCode) {
            case PayoneCODPaymentMethod::PAYMENT_CODE:
                return pluginApp(PayoneCODPaymentMethod::class);
            case PayoneInvoicePaymentMethod::PAYMENT_CODE:
                return pluginApp(PayoneInvoicePaymentMethod::class);
            case PayonePaydirektPaymentMethod::PAYMENT_CODE:
                return pluginApp(PayonePaydirektPaymentMethod::class);
            case PayonePayolutionInstallmentPaymentMethod::PAYMENT_CODE:
                return pluginApp(PayonePayolutionInstallmentPaymentMethod::class);
            case PayonePayPalPaymentMethod::PAYMENT_CODE:
                return pluginApp(PayonePayPalPaymentMethod::class);
            case PayonePrePaymentPaymentMethod::PAYMENT_CODE:
                return pluginApp(PayonePrePaymentPaymentMethod::class);
            case PayoneRatePayInstallmentPaymentMethod::PAYMENT_CODE:
                return pluginApp(PayoneRatePayInstallmentPaymentMethod::class);
            case PayoneSofortPaymentMethod::PAYMENT_CODE:
                return pluginApp(PayoneSofortPaymentMethod::class);
            case PayoneCCPaymentMethod::PAYMENT_CODE:
                return pluginApp(PayoneCCPaymentMethod::class);
            case PayoneDirectDebitPaymentMethod::PAYMENT_CODE:
                return pluginApp(PayoneDirectDebitPaymentMethod::class);
            case PayoneInvoiceSecurePaymentMethod::PAYMENT_CODE;
                return pluginApp(PayoneInvoiceSecurePaymentMethod::class);
        }
        throw new \InvalidArgumentException('Unknown payment method ' . $paymentCode);
    }
}
