<?php

namespace PayoneApi\Request\Refund;

use PayoneApi\Request\GenericRequest;

class Refund
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
     * Refund constructor.
     *
     * @param GenericRequest $request
     * @param string $txid
     */
    public function __construct(GenericRequest $request, string $txid)
    {
        $this->txid = $txid;
        $this->request = $request;
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
}
