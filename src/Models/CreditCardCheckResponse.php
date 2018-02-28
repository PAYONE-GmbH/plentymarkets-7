<?php

namespace Payone\Models;

class CreditCardCheckResponse implements \JsonSerializable
{
    /** @var string */
    private $status;

    /** @var string */
    private $pseudocardpan;

    /** @var string */
    private $truncatedcardpan;

    /** @var string */
    private $cardtype;

    /** @var string */
    private $cardexpiredate;

    /**
     * Getter for Status
     *
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * Getter for Pseudocardpan
     *
     * @return string
     */
    public function getPseudocardpan(): string
    {
        return $this->pseudocardpan;
    }

    /**
     * Getter for Truncatedcardpan
     *
     * @return string
     */
    public function getTruncatedcardpan(): string
    {
        return $this->truncatedcardpan;
    }

    /**
     * Getter for Cardtype
     *
     * @return string
     */
    public function getCardtype(): string
    {
        return $this->cardtype;
    }

    /**
     * Getter for Cardexpiredate
     *
     * @return string
     */
    public function getCardexpiredate(): string
    {
        return $this->cardexpiredate;
    }

    /**
     * @param string $status
     * @param string $pseudocardpan
     * @param string $truncatedcardpan
     * @param string $cardtype
     * @param string $cardexpiredate 4 digit Payone date e.g. 1802 for 28-02-2018
     */
    public function init(
        $status,
        $pseudocardpan,
        $truncatedcardpan,
        $cardtype,
        $cardexpiredate
    ) {
        $this->status = $status;
        $this->pseudocardpan = $pseudocardpan;
        $this->truncatedcardpan = $truncatedcardpan;
        $this->cardtype = $cardtype;

        $year = 2000 + (int) substr($cardexpiredate, 0, 2);
        $month = (int) substr($cardexpiredate, 2, 4);

        $datetime = \DateTime::createFromFormat('Y-m-d', $year . '-' . $month . '-01');
        $this->cardexpiredate = $datetime->format('Y-m-t');
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'pseudocardpan' => $this->getPseudocardpan(),
            'truncatedcardpan' => $this->getTruncatedcardpan(),
            'cardtype' => $this->getCardtype(),
            'cardexpiredate' => $this->getCardexpiredate(),
        ];
    }
}
