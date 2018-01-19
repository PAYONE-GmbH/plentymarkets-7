<?php

namespace Payone\Validator;

use Payone\Models\PaymentConfig\CreditCardExpiration;

class CardExpireDate
{
    /**
     * @var CreditCardExpiration
     */
    private $expireDateRepo;

    /**
     * CardExpireDate constructor.
     *
     * @param CardExpireDate $expireDateRepo
     */
    public function __construct(CreditCardExpiration $expireDateRepo)
    {
        $this->expireDateRepo = $expireDateRepo;
    }

    /**
     * @param \DateTime $expireDate
     * @param \DateTime $today
     *
     * @return bool
     * @throws \Exception
     */
    public function validate(\DateTime $expireDate, \DateTime $today = null)
    {
        if (!$today) {
            $today = new \DateTime();
        }
        $difference = date_diff($today, $expireDate);

        $daysValid = $difference->days;

        $isExpireDateInFuture = $expireDate->getTimestamp() > $today->getTimestamp();
        $isEypiryTimeLongEnough = $daysValid >= (int)$this->expireDateRepo->getMinExpireTimeInDays();
        if (!$isExpireDateInFuture || !$isEypiryTimeLongEnough) {
            throw new \Exception('Credit card expires too soon. Please choose another payment method.');
        }

        return true;
    }
}
