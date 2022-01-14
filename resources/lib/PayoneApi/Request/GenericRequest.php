<?php

namespace PayoneApi\Request;

use PayoneApi\Request\Parts\Config;
use PayoneApi\Request\Parts\SystemInfo;
use PayoneApi\Request\Parts\Cart;

/**
 * Class GenericRequest
 */
class GenericRequest implements RequestDataContract
{
    /**
     * @var string
     */
    protected $request;

    /**
     * @var int
     */
    private $amount;

    /**
     * @var string
     */
    private $currency;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var string|null
     */
    private $sequencenumber;

    /**
     * @var SystemInfo
     */
    private $info;

    /**
     * @var Cart
     */
    private $cart;

    /**
     * GenericRequest constructor.
     *
     * @param Config $config
     * @param string $request
     * @param int $amount
     * @param string $currency
     * @param string|null $sequencenumber
     * @param SystemInfo $info
     * @param Cart $cart
     */
    public function __construct(
        Config $config,
               $request,
        int $amount,
               $currency,
        SystemInfo $info,
        Cart $cart,
        $sequencenumber = null

    ) {
        $this->config = $config;
        $this->request = $request;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->sequencenumber = $sequencenumber;
        $this->info = $info;
        $this->cart = $cart;
    }

    /**
     * Getter for Sequencenumber
     */
    public function getSequencenumber()
    {
        return $this->sequencenumber;
    }

    /**
     * Getter for Amount
     *
     * @return int
     */
    public function getAmount()
    {
        return $this->amount;
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
     * Getter for Config
     *
     * @return Config
     */
    public function getConfig(): Config
    {
        return $this->config;
    }

    /**
     * @return string
     */
    public function getRequest()
    {
        return $this->request;
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
     * @return Cart
     */
    public function getCart(): Cart
    {
        return $this->cart;
    }


}
