<?php

namespace Payone\Models;

use Payone\Adapter\Config as ConfigAdapter;
use Payone\Methods\PayoneCCPaymentMethod;

class CreditcardTypes
{
    const VISA = 'V';
    const MASTERCARD = 'M';
    const AMEX = 'A';
    const MAESTRO_INT = 'O';
    const MAESTRO_UK = 'U';
    const DINERS = 'D';
    const CARTE_BLEUE = 'B';
    const DISCOVER = 'C';
    const JCB = 'J';
    const CHINA_UNION_PAY = 'P';

    /** @var ConfigAdapter */
    private $configRepo;

    /**
     * Api constructor.
     *
     * @param $paymentCode
     * @param ConfigAdapter $configRepo
     */
    public function __construct(
        ConfigAdapter $configRepo
    ) {
        $this->configRepo = $configRepo;
    }

    /**
     * @return string[]
     */
    public function getAllowedTypes(): array
    {
        $allowedTypesFromConfigString = $this->configRepo->get(PayoneCCPaymentMethod::PAYMENT_CODE . '.allowedCardTypes');
        if ($allowedTypesFromConfigString == ConfigAdapter::MULTI_SELECT_ALL) {
            return $this->getCreditCardTypes();
        }

        $allowedTypesFromConfigString = str_replace(
            'PAYONE_PAYONE_CREDIT_CARD.allowedCardTypes.',
            '',
            $allowedTypesFromConfigString
        );

        $allowedTypesFromConfig = explode(', ', $allowedTypesFromConfigString);

        return array_intersect($this->getCreditCardTypes(), $allowedTypesFromConfig);
    }

    /**
     * @return string[]
     */
    public function getCreditCardTypes(): array
    {
        return [
            self::VISA,
            self::MASTERCARD,
            self::AMEX,
            self::MAESTRO_INT,
            self::MAESTRO_UK,
            self::DINERS,
            self::CARTE_BLEUE,
            self::DISCOVER,
            self::JCB,
            self::CHINA_UNION_PAY,
        ];
    }
}
