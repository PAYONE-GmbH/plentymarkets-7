<?php

namespace PayoneApi\Request\GenericPayment;

use PayoneApi\Request\ClearingTypes;
use PayoneApi\Request\Parts\Config;
use PayoneApi\Request\Parts\SystemInfo;
use PayoneApi\Request\Types;

class AmazonPayGetOrderReferenceRequest
{

    private $request = Types::GENERICPAYMENT;

    private $clearingtype = ClearingTypes::WALLET;

    private $wallettype = "AMZ";

    private $amount;

    private $currency;

    private $workorderid;

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
     * @param string $workOrderId
     * @param string $currency
     */
    public function __construct(
        Config $config,
        SystemInfo $info,
        string $amazonReferenceId,
        string $amazonAddressToken,
        string $workOrderId,
        string $currency
    )
    {
        $this->config = $config;
        $this->info = $info;
        $this->add_paydata['amazon_reference_id'] = $amazonReferenceId;
        $this->add_paydata['amazon_address_token'] = $amazonAddressToken;
        $this->workorderid = $workOrderId;
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
