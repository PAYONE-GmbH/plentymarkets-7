<?php


namespace Payone\Models;

class PayonePaymentStatus
{
    const APPOINTED = 'appointed';// transaction_status pending / completed
    const APPOINTED_PENDING = 'appointed_complete';// transaction_status pending / completed
    const APPOINTED_COMPLETE = 'appointed_pending';// transaction_status pending / completed
    const CAPTURE = 'capture';
    const  PAID = 'paid';
    const UNDERPAID = 'underpaid';
    const CANCELLATION = 'cancelation';
    const REFUND = 'refund';
    const DEBIT = 'debit';
    const TRANSFER = 'transfer';

    /**
     * Status after this comment have to be activated by Payone or are not implemented yet
     */
    const  REMINDER = 'reminder';
    const VAUTHORIZATION = 'vauthorization';
    const VSETTLEMENT = 'vsettlement';
    const INVOICE = 'invoice';
    const FAILED = 'failed';

    /**
     * @param string $payoneStatus
     * @return int
     * @throws \Exception
     */
    public static function getPlentyStatus(string $payoneStatus)
    {
        switch ($payoneStatus) {
            case self::APPOINTED:
            case self::APPOINTED_COMPLETE:
            case self::CAPTURE:
            case self::PAID:
                return PaymentStatus::CAPTURED;
            case self::APPOINTED_PENDING:
                return PaymentStatus::AWAITING_APPROVAL;
            case self::UNDERPAID:
                return PaymentStatus::PARTIALLY_CAPTURED;
            case self::CANCELLATION:
                return PaymentStatus::CANCELLED;
            case self::REFUND:
                return PaymentStatus::REFUNDED;
        }
        throw new \Exception('Payment status "' . $payoneStatus . '" not mapped to plentymarkets status.');
    }
}