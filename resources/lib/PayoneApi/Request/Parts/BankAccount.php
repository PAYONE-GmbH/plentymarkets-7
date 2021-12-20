<?php

namespace PayoneApi\Request\Parts;

class BankAccount
{
    /**
     * @var string
     */
    protected $bankcountry;

    /**
     * @var string
     */
    protected $iban;

    /**
     * @var string
     */
    protected $bic;

    /**
     * @var string
     */
    protected $firstname;

    /**
     * @var string
     */
    protected $lastname;

    /**
     * BankAccount constructor.
     *
     * @param string $bankcountry
     * @param string $holder
     * @param string $iban
     * @param string $bic
     */
    public function __construct(string $bankcountry, string $holder, string $iban, string $bic)
    {
        $this->bankcountry = $bankcountry;
        $this->iban = $iban;
        $this->bic = $bic;
        $holderNames = explode(' ', $holder);
        $this->firstname = $holderNames[0] ?? '';
        $this->lastname = $holderNames[1] ?? '';
    }

    /**
     * Getter for Bankcountry
     *
     * @return string
     */
    public function getBankcountry()
    {
        return $this->bankcountry;
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
     * Getter for Firstname
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Getter for Lastname
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }
}
