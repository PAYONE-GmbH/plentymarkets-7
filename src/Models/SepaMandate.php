<?php

namespace Payone\Models;

class SepaMandate implements \JsonSerializable
{
    /**
     * string
     */
    private $identification;

    /**
     * string
     */
    private $status;

    /**
     * string
     */
    private $text;

    /**
     * string
     */
    private $creditorIdentifier;

    /**
     * string
     */
    private $iban;

    /**
     * string
     */
    private $bic;

    /**
     * @param $identification
     * @param $status
     * @param $text
     * @param $creditorIdentifier
     * @param $iban
     * @param $bic
     */
    public function init(
        $identification,
        $status,
        $text,
        $creditorIdentifier,
        $iban,
        $bic
    ) {
        $this->identification = $identification;
        $this->status = $status;
        $this->text = $text;
        $this->creditorIdentifier = $creditorIdentifier;
        $this->iban = $iban;
        $this->bic = $bic;
    }

    /**
     * Getter for Identification
     *
     * @return string
     */
    public function getIdentification()
    {
        return $this->identification;
    }

    /**
     * Getter for Status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Getter for Text
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Getter for CreditorIdentifier
     *
     * @return string
     */
    public function getCreditorIdentifier()
    {
        return $this->creditorIdentifier;
    }

    /**
     * Getter for Iban
     *
     * @return string
     */
    public function getIban()
    {
        return $this->iban;
    }

    /**
     * Getter for Bic
     *
     * @return string
     */
    public function getBic()
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
        return strtoupper(substr($this->getIban(), 0, 2));
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'status' => $this->getStatus(),
            'identification' => $this->getIdentification(),
            'text' => $this->getText(),
            'creditorIdentifier' => $this->getCreditorIdentifier(),
            'iban' => $this->getIban(),
            'bic' => $this->getBic(),
            'bankcountry' => $this->getCountry(),
        ];
    }
}
