<?php

namespace Payone\Models;

class PayonePaymentStatus
{
    const APPOINTED = 'appointed';
    const APPOINTED_PENDING = 'appointed_pending';
    const APPOINTED_COMPLETE = 'appointed_completed';
    const CAPTURE = 'capture';
    const PAID = 'paid';
    const UNDERPAID = 'underpaid';
    const CANCELLATION = 'cancelation';
    const REFUND = 'refund';
    const DEBIT = 'debit';
    const TRANSFER = 'transfer';

    /**
     * Status after this comment have to be activated by Payone or are not implemented yet
     */
    const REMINDER = 'reminder';
    const VAUTHORIZATION = 'vauthorization';
    const VSETTLEMENT = 'vsettlement';
    const INVOICE = 'invoice';
    const FAILED = 'failed';

    /**
     * @param string $payoneStatus
     *
     * @throws \Exception
     *
     * @return int
     */
    public static function getPlentyStatus(string $payoneStatus)
    {
        switch ($payoneStatus) {
            case self::APPOINTED:
            case self::APPOINTED_COMPLETE:
            case self::CAPTURE:
            case self::PAID:
            case self::TRANSFER:
                return PaymentStatus::CAPTURED;
            case self::APPOINTED_PENDING:
                return PaymentStatus::AWAITING_APPROVAL;
            case self::UNDERPAID:
                return PaymentStatus::PARTIALLY_CAPTURED;
            case self::CANCELLATION:
                return PaymentStatus::CANCELLED;
            case self::REFUND:
            case self::DEBIT:
                return PaymentStatus::REFUNDED;
        }
        throw new \Exception('Payment status "' . $payoneStatus . '" not mapped to plentymarkets status.');
    }
}
