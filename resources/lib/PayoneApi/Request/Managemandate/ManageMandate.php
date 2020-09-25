<?php

namespace PayoneApi\Request\Managemandate;

use PayoneApi\Request\ClearingTypes;
use PayoneApi\Request\Parts\BankAccount;
use PayoneApi\Request\Parts\Config;
use PayoneApi\Request\Parts\Customer;
use PayoneApi\Request\Parts\SystemInfo;
use PayoneApi\Request\RequestDataContract;
use PayoneApi\Request\Types;

class ManageMandate implements RequestDataContract
{
    /**
     * @var string
     */
    protected $clearingtype = ClearingTypes::DEBIT_PAYMENT;

    /**
     * @var string
     */
    protected $mandateIdentification;

    /**
     * @var string
     */
    protected $currency;

    /**
     * @var BankAccount
     */
    protected $bankAccount;

    /**
     * @var Customer
     */
    protected $customer;

    /**
     * @var string
     */
    protected $request = Types::MANAGEMANDATE;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var SystemInfo
     */
    protected $info;

    /**
     * ManageMandate constructor.
     *
     * @param Config $config
     * @param string $currency
     * @param Customer $customer
     * @param SystemInfo $info
     * @param BankAccount $bankAccount
     * @param string $mandateIdentification
     */
    public function __construct(
        Config $config,
        string $currency,
        Customer $customer,
        SystemInfo $info,
        BankAccount $bankAccount,
        string $mandateIdentification = ''
    ) {
        $this->config = $config;
        $this->currency = $currency;
        $this->customer = $customer;
        $this->info = $info;
        $this->bankAccount = $bankAccount;
        $this->mandateIdentification = $mandateIdentification;
    }

    /**
     * Getter for Clearingtype
     *
     * @return string
     */
    public function getClearingtype()
    {
        return $this->clearingtype;
    }

    /**
     * Getter for MandateIdentification
     *
     * @return string
     */
    public function getMandateIdentification()
    {
        return $this->mandateIdentification;
    }

    /**
     * Getter for Currency
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Getter for Request
     *
     * @return string
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Getter for BankAccount
     *
     * @return BankAccount
     */
    public function getBankAccount()
    {
        return $this->bankAccount;
    }

    /**
     * Getter for Customer
     *
     * @return Customer
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * Getter for Config
     *
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Getter for Info
     *
     * @return SystemInfo
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * Getter for Sequencenumber
     * @return null
     */
    public function getSequencenumber()
    {
        return null;
    }

    /**
     * Getter for Amount
     *
     * @return null
     */
    public function getAmount()
    {
        return null;
    }
}
