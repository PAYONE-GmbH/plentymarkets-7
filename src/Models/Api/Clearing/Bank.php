<?php

namespace Payone\Models\Api\Clearing;

class Bank extends ClearingAbstract
{
    private $account;
    private $code;
    private $country;
    private $name;
    private $accountholder;
    private $city;
    private $iban;
    private $bic;

    /**
     * @param $account
     * @param $code
     * @param $country
     * @param $name
     * @param $accountholder
     * @param $city
     * @param $iban
     * @param $bic
     *
     * @return $this
     */
    public function init(
        $account,
        $code,
        $country,
        $name,
        $accountholder,
        $city,
        $iban,
        $bic
    ) {
        $this->account = $account;
        $this->code = $code;
        $this->country = $country;
        $this->name = $name;
        $this->accountholder = $accountholder;
        $this->city = $city;
        $this->iban = $iban;
        $this->bic = $bic;

        return $this;
    }

    /**
     * Getter for Account
     *
     * @return mixed
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * Getter for Code
     *
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Getter for Country
     *
     * @return mixed
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Getter for Name
     *
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Getter for Accountholder
     *
     * @return mixed
     */
    public function getAccountholder()
    {
        return $this->accountholder;
    }

    /**
     * Getter for City
     *
     * @return mixed
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Getter for Iban
     *
     * @return mixed
     */
    public function getIban()
    {
        return $this->iban;
    }

    /**
     * Getter for Bic
     *
     * @return mixed
     */
    public function getBic()
    {
        return $this->bic;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'account' => $this->account,
            'code' => $this->code,
            'country' => $this->country,
            'name' => $this->name,
            'accountholder' => $this->accountholder,
            'city' => $this->city,
            'iban' => $this->iban,
            'bic' => $this->bic,
        ];
    }
}
