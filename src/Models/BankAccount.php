<?php

namespace Payone\Models;

/**
 * Class BankAccount
 */
class BankAccount implements \JsonSerializable
{
    /**
     * @var string
     */
    private $holder = '';
    /**
     * @var string
     */
    private $iban = '';

    /**
     * @var string
     */
    private $bic = '';

    /**
     * @param $holder
     * @param $iban
     * @param $bic
     *
     * @return $this
     */
    public function init($holder, $iban, $bic)
    {
        $this->holder = $holder;
        $this->iban = $iban;
        $this->bic = $bic;

        return $this;
    }

    /**
     * Getter for Holder
     *
     * @return string
     */
    public function getHolder(): string
    {
        return $this->holder;
    }

    /**
     * Getter for Iban
     *
     * @return string
     */
    public function getIban(): string
    {
        return str_replace(' ', '', $this->iban);
    }

    /**
     * Getter for Bic
     *
     * @return string
     */
    public function getBic(): string
    {
        return $this->bic;
    }

    /**
     * Getter account country
     *
     * @return string
     */
    public function getCountry(): string
    {
        if(!$this->getIban()){
            return '';
        }

        return strtoupper(substr($this->getIban(), 0, 2));
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'holder' => $this->getHolder(),
            'iban' => $this->getIban(),
            'bic' => $this->getBic(),
            'country' => $this->getCountry(),
        ];
    }
}
