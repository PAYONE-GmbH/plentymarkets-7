<?php

namespace PayoneApi\Request\Debit;

use PayoneApi\Request\GenericRequest;
use PayoneApi\Request\Parts\Cart;

class Debit
{
    /**
     * @var string
     */
    protected $txid;

    /**
     * @var GenericRequest
     */
    protected $request;

    /**
     * @var Cart
     */
    protected $cart;

    /**
     * Debit constructor.
     *
     * @param GenericRequest $request
     * @param string $txid
     * @param Cart|null $cart
     */
    public function __construct(GenericRequest $request, string $txid, Cart $cart = null)
    {
        $this->txid = $txid;
        $this->request = $request;
        $this->cart = $cart;
    }

    /**
     * Getter for Request
     *
     * @return GenericRequest
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Getter for Txid
     *
     * @return string
     */
    public function getTxid()
    {
        return $this->txid;
    }

    /**
     * Getter for Cart
     * @return Cart
     */
    public function getCart()
    {
        return $this->cart;
    }
}
