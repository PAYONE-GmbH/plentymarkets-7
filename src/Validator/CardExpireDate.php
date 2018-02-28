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
     * @throws \Exception
     *
     * @return bool
     */
    public function validate(\DateTime $expireDate, \DateTime $today = null)
    {
        $minExpireTimeInDays = (int)$this->expireDateRepo->getMinExpireTimeInDays();
        if ($minExpireTimeInDays < 1) {
            return true;
        }
        if (!$today) {
            $today = \DateTime::createFromFormat('Y-m-d', date('Y-m-d'));
        }
        $difference = date_diff($today, $expireDate);

        $daysValid = $difference->days;

        $isExpireDateInFuture = $expireDate->getTimestamp() > $today->getTimestamp();
        $isEypiryTimeLongEnough = $daysValid >= $minExpireTimeInDays;
        if (!$isExpireDateInFuture || !$isEypiryTimeLongEnough) {
            throw new \Exception('Credit card expires too soon. Please choose another payment method.');
        }

        return true;
    }
}
