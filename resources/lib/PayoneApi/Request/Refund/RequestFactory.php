<?php

namespace PayoneApi\Request\Refund;

use PayoneApi\Request\GenericRequestFactory;
use PayoneApi\Request\RequestFactoryContract;
use PayoneApi\Request\Types;

class RequestFactory implements RequestFactoryContract
{
    /**
     * @param string $paymentMethod
     * @param array $data
     * @param string|null $referenceId
     *
     * @return Refund|\PayoneApi\Request\RequestDataContract
     */
    public static function create(string $paymentMethod, array $data, string $referenceId = null)
    {
        $genericRequest = GenericRequestFactory::create(Types::REFUND, $data);

        return new Refund($genericRequest, $referenceId);
    }
}
