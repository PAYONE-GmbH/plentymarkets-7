<?php

namespace PayoneApi\Request\Genericpayment;

use PayoneApi\Request\ClearingTypes;
use PayoneApi\Request\Parts\Config;
use PayoneApi\Request\Parts\SystemInfo;
use PayoneApi\Request\Types;

class AmazonPayConfiguration
{
    private $request = Types::GENERICPAYMENT;

    private $clearingtype = ClearingTypes::WALLET;

    private $wallettype = "AMZ";

    private $add_paydata = ['action' => 'getconfiguration'];

    private $currency;

    /**
     * @var Config
     */
    private $config;
    /**
     * @var SystemInfo
     */
    private $info;

    /**
     * ManageMandate constructor.
     *
     * @param Config $config
     * @param SystemInfo $info
     * @param string $currency
     */
    public function __construct(
        Config $config,
        SystemInfo $info,
        string $currency
    )
    {
        $this->config = $config;
        $this->info = $info;
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
     * @return array
     */
    public function getAddPaydata(): array
    {
        return $this->add_paydata;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
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
