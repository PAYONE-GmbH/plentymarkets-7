<?php

namespace PayoneApi\Request\GenericPayment;

use PayoneApi\Request\Parts\Config;
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
     * @param Config $config
     * @param SystemInfo $info
     * @param string $currency
     * @param string $amount
     * @param string $financingtype
     * @param ShippingAddress $shippingAddress
     */
   public function __construct(
        Config $config,
        SystemInfo $info,
        string $currency,
        string $amount,
        string $financingtype,
        ShippingAddress $shippingAddress
    )
    {
        parent::__construct(
            [
                'action' => 'start_session',
            ],
            $config,
            $info,
            $currency,
            $amount
        );

        $this->financingtype = $financingtype;
        $this->shippingAddress = $shippingAddress;

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



}
