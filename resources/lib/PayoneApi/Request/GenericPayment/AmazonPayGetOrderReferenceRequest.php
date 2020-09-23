<?php

namespace PayoneApi\Request\GenericPayment;

use PayoneApi\Request\ClearingTypes;
use PayoneApi\Request\Parts\Config;
use PayoneApi\Request\Parts\SystemInfo;
use PayoneApi\Request\Types;
use PayoneApi\Request\WalletTypes;

class AmazonPayGetOrderReferenceRequest
{
    /** @var string  */
    private $request = Types::GENERICPAYMENT;

    /** @var string  */
    private $clearingtype = ClearingTypes::WALLET;

    /** @var string  */
    private $wallettype = WalletTypes::AMAZON_PAYMENTS;

    /** @var string  */
    private $currency;

    /** @var string  */
    private $amount;

    /** @var string  */
    private $workorderid;

    /** @var array  */
    private $add_paydata = [
        'action' => 'getorderreferencedetails',
        'amazon_reference_id' => '',
        'amazon_address_token' => '',
    ];

    /** @var Config */
    private $config;

    /** @var SystemInfo */
    private $info;

    /**
     * AmazonPayGetOrderReferenceRequest constructor.
     *
     * @param Config $config
     * @param SystemInfo $info
     * @param string $amazonReferenceId
     * @param string $amazonAddressToken
     * @param string $workOrderId
     * @param string $amount
     * @param string $currency
     */
    public function __construct(
        Config $config,
        SystemInfo $info,
        string $amazonReferenceId,
        string $amazonAddressToken,
        string $workOrderId,
        string $amount,
        string $currency
    )
    {
        $this->config = $config;
        $this->info = $info;
        $this->add_paydata['amazon_reference_id'] = $amazonReferenceId;
        $this->add_paydata['amazon_address_token'] = $amazonAddressToken;
        $this->workorderid = $workOrderId;
        $this->amount = $amount;
        $this->currency = $currency;
    }

    /**
     * @return string
     */
    public function getRequest(): string
    {
        return $this->request;
    }

    /**
     * @return string
     */
    public function getClearingtype(): string
    {
        return $this->clearingtype;
    }

    /**
     * @return string
     */
    public function getWallettype(): string
    {
        return $this->wallettype;
    }

    /**
     * @return mixed
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @return string
     */
    public function getWorkorderid(): string
    {
        return $this->workorderid;
    }

    /**
     * @return array
     */
    public function getAddPaydata(): array
    {
        return $this->add_paydata;
    }

    /**
     * @return Config
     */
    public function getConfig(): Config
    {
        return $this->config;
    }

    /**
     * @return SystemInfo
     */
    public function getInfo(): SystemInfo
    {
        return $this->info;
    }

}
