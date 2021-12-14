<?php


namespace PayoneApi\Request\GenericPayment;


use PayoneApi\Request\ClearingTypes;
use PayoneApi\Request\Parts\Config;
use PayoneApi\Request\Parts\SystemInfo;
use PayoneApi\Request\Types;

class GenericKlarnaPayRequestBase
{
    /**
     * @var string
     */
    protected $request = Types::GENERICPAYMENT;

    /**
     * @var string
     */
    protected $clearingtype = ClearingTypes::FINANCING;

    /**
     * @var array
     */
    protected $addPaydata = [];

    /**
     * @var string
     */
    protected $currency;

    /**
     * @var string
     */
    protected $amount;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var SystemInfo
     */
    protected $info;

    /**
     * GenericeRequestBase constructor.
     * @param array $payData
     * @param Config $config
     * @param SystemInfo $info
     * @param string $currency
     * @param string $amount
     */
    public function __construct(
        array $payData,
        Config $config,
        SystemInfo $info,
        string $currency,
        string $amount
    )
    {
        $this->addPaydata = $payData;
        $this->config = $config;
        $this->info = $info;
        $this->currency = $currency;
        $this->amount = $amount;
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
     * @return array
     */
    public function getAddPaydata(): array
    {
        return $this->addPaydata;
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

    /**
     * @return string
     */
    public function getAmount(): string
    {
        return $this->amount;
    }


}
