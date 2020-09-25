<?php

namespace PayoneApi\Request\Capture;

use PayoneApi\Request\GenericRequest;
use PayoneApi\Request\Parts\Cart;

class Capture
{
    /**
     * @var string
     */
    protected $txid;

    /**
     * @var string
     */
    protected $capturemode;

    /**
     * @var Cart|null
     */
    protected $cart;

    /**
     * @var GenericRequest
     */
    protected $request;

    /**
     * @var string
     */
    protected $settleaccount;

    /**
     * Capture constructor.
     *
     * @param GenericRequest $request
     * @param string $txid
     * @param string $capturemode
     * @param string $settleaccount
     * @param Cart $cart
     */
    public function __construct(
        GenericRequest $request,
        string $txid,
        string $capturemode,
        string $settleaccount,
        Cart $cart = null
    ) {
        $this->request = $request;
        $this->txid = $txid;
        $this->capturemode = $capturemode;
        $this->cart = $cart;
        $this->settleaccount = $settleaccount;
    }

    /**
     * Getter for Cart
     * @return Cart
     */
    public function getCart()
    {
        return $this->cart;
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
     * Getter for Capturemode
     *
     * @return string
     */
    public function getCapturemode()
    {
        return $this->capturemode;
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
     * Getter for Settleaccount
     * @return string
     */
    public function getSettleaccount()
    {
        return $this->settleaccount;
    }


}
