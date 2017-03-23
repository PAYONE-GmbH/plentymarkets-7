<?php

namespace Payone\Models;

/**
 * Class PaymentStatus
 */
class PaymentStatus
{
    /**
     * @see https://developers.plentymarkets.com/rest-doc/introduction#payment-statuses
     */
    const AWAITING_APPROVAL = 1;
    const APPROVED = 2;
    const CAPTURED = 3;
    const PARTIALLY_CAPTURED = 4;
    const CANCELLED = 5;
    const REFUSED = 6;
    const AWAITING_RENEWAL = 7;
    const EXPIRED = 8;
    const REFUNDED = 9;
    const PARTIALLY_REFUNDED = 10;
}
