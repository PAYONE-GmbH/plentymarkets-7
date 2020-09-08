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

    private $currency;

    private $workorderid;

    private $add_paydata = [
        'action' => 'getconfiguration',
        'amazon_address_token' => '',
        'amazon_reference_id' => '',
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
     * @param string $amazonAddressToken
     * @param string $amazonReferenceId
     * @param string $workOrderId
     * @param string $currency
     */
    public function __construct(
        Config $config,
        SystemInfo $info,
        string $amazonAddressToken,
        string $amazonReferenceId,
        string $workOrderId,
        string $currency
    )
    {
        $this->config = $config;
        $this->info = $info;
        $this->add_paydata['amazon_address_token'] = $amazonAddressToken;
        $this->add_paydata['amazon_reference_id'] = $amazonReferenceId;
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
