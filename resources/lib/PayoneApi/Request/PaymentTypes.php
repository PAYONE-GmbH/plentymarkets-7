<?php

namespace PayoneApi\Request;

/**
 * Class PaymentTypes
 */
class PaymentTypes
{
    const PAYONE_INVOICE = 'Invoice';
    const PAYONE_PRE_PAYMENT = 'PrePayment';
    const PAYONE_CASH_ON_DELIVERY = 'CashOnDelivery';
    const PAYONE_SOFORT = 'Sofort';
    const PAYONE_CREDIT_CARD = 'CreditCard';
    const PAYONE_DIRECT_DEBIT = 'DirectDebit';
    const PAYONE_PAY_PAL = 'PayPal';
    const PAYONE_PAYDIREKT = 'Paydirekt';
    const PAYONE_INVOICE_SECURE = 'InvoiceSecure';
    const PAYONE_ON_LINE_BANK_TRANSFER = 'OnlineBankTransfer';
    const PAYONE_AMAZON_PAY = 'Amazon Pay';

    /**
     * @return mixed
     */
    public static function getPaymentTypes()
    {
        $oClass = new \ReflectionClass(__CLASS__);

        return $oClass->getConstants();
    }
}
