<?php

namespace PayoneApi\Request\GenericPayment;

use PayoneApi\Request\Parts\Cart;
use PayoneApi\Request\Parts\Config;
use PayoneApi\Request\Parts\Customer;
use PayoneApi\Request\Parts\SystemInfo;
use PayoneApi\Request\Parts\ShippingAddress;

class KlarnaStartSessionRequest extends GenericKlarnaPayRequestBase
{
    /**
     * @var string
     */
    protected $financingtype;

    /**
     * @var ShippingAddress
     */
    protected $shippingAddress;

    /**
     * @var string
     */
    private $successurl;

    /**
     * @var string
     */
    private $errorurl;

    /**
     * @var string
     */
    private $backurl;

    /**
     * @var Cart
     */
    private $cart;
    /**
     * @var Customer
     */
    private $customer;


    /**
     * @param Config $config
     * @param SystemInfo $info
     * @param string $currency
     * @param string $amount
     * @param string $financingtype
     * @param string $successurl
     * @param string $errorurl
     * @param string $backurl
     * @param Cart $cart
     * @param ShippingAddress $shippingAddress
     * @param Customer $customer
     * @param string $shippingEmail
     * @param string $shippingTitle
     * @param string $shippingTelephonenumber
     */
    public function __construct(
        Config $config,
        SystemInfo $info,
        string $currency,
        string $amount,
        string $financingtype,
        ShippingAddress $shippingAddress,
        string $successurl,
        string $errorurl,
        string  $backurl,
        Cart $cart,
        Customer $customer,
        $shippingEmail,
        $shippingTitle,
        $shippingTelephonenumber

    )
    {
        parent::__construct(
            [
                'action' => 'start_session',
                'shipping_email' => $shippingEmail,
                'shipping_title' => $shippingTitle,
                'shipping_telephonenumber' => $shippingTelephonenumber
            ],
            $config,
            $info,
            $currency,
            $amount
        );

        $this->financingtype = $financingtype;
        $this->shippingAddress = $shippingAddress;
        $this->successurl = $successurl;
        $this->errorurl = $errorurl;
        $this->backurl = $backurl;
        $this->cart = $cart;
        $this->customer = $customer;

    }

    /**
     * @return string
     */
    public function getFinancingtype(): string
    {
        return $this->financingtype;
    }

    /**
     * @return ShippingAddress
     */
    public function getShippingAddress(): ShippingAddress
    {
        return $this->shippingAddress;
    }

    /**
     * @return string
     */
    public function getSuccessurl(): string
    {
        return $this->successurl;
    }

    /**
     * @return string
     */
    public function getErrorurl(): string
    {
        return $this->errorurl;
    }

    /**
     * @return string
     */
    public function getBackurl(): string
    {
        return $this->backurl;
    }

    /**
     * @return Cart
     */
    public function getCart(): Cart
    {
        return $this->cart;
    }

    /**
     * @return Customer
     */
    public function getCustomer(): Customer
    {
        return $this->customer;
    }
}
