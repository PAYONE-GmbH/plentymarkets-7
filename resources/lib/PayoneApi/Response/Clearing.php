<?php

namespace PayoneApi\Response;

/**
 * Class Clearing
 */
class Clearing extends ResponseDataAbstract
{
    /**
     * @var string
     */
    protected $bankaccount;

    /**
     * @var string
     */
    protected $bankcode;

    /**
     * @var string
     */
    protected $bankcountry;

    /**
     * @var string
     */
    protected $bankname;

    /**
     * @var string
     */
    protected $bankaccountholder;

    /**
     * @var string
     */
    protected $bankcity;

    /**
     * @var string
     */
    protected $bankiban;

    /**
     * @var string
     */
    protected $bankbic;

    /**
     * Clearing constructor.
     *
     * @param array $responseData
     */
    public function __construct(array $responseData)
    {
        $this->bankaccount = $responseData['clearing_bankaccount'] ?? '';
        $this->bankcode = $responseData['clearing_bankcode'] ?? '';
        $this->bankcountry = $responseData['clearing_bankcountry'] ?? '';
        $this->bankname = $responseData['clearing_bankname'] ?? '';
        $this->bankaccountholder = $responseData['clearing_bankaccountholder'] ?? '';
        $this->bankcity = $responseData['clearing_bankcity'] ?? '';
        $this->bankiban = $responseData['clearing_bankiban'] ?? '';
        $this->bankbic = $responseData['clearing_bankbic'] ?? '';
    }

    /**
     * Getter for Bankaccount
     *
     * @return string
     */
    public function getBankaccount()
    {
        return $this->bankaccount;
    }

    /**
     * Getter for Bankcode
     *
     * @return string
     */
    public function getBankcode()
    {
        return $this->bankcode;
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
     * Getter for Bankname
     *
     * @return string
     */
    public function getBankname()
    {
        return $this->bankname;
    }

    /**
     * Getter for Bankaccountholder
     *
     * @return string
     */
    public function getBankaccountholder()
    {
        return $this->bankaccountholder;
    }

    /**
     * Getter for Bankcity
     *
     * @return string
     */
    public function getBankcity()
    {
        return $this->bankcity;
    }

    /**
     * Getter for Bankiban
     *
     * @return string
     */
    public function getBankiban()
    {
        return $this->bankiban;
    }

    /**
     * Getter for Bankbic
     *
     * @return string
     */
    public function getBankbic()
    {
        return $this->bankbic;
    }
}
