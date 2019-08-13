<?php

namespace Payone\ArvPayoneApi\Request\Refund;

use Payone\ArvPayoneApi\Request\GenericRequestFactory;
use Payone\ArvPayoneApi\Request\RequestFactoryContract;
use Payone\ArvPayoneApi\Request\Types;

class RequestFactory implements RequestFactoryContract
{
    /**
     * @param string $paymentMethod
     * @param array $data
     * @param null $referenceId
     *
     * @return Refund|\Payone\ArvPayoneApi\Request\RequestDataContract
     */
    public static function create($paymentMethod, $data, $referenceId = null)
    {
        $genericRequest = GenericRequestFactory::create(Types::REFUND, $data);

        return new Refund($genericRequest, $referenceId);
    }
}
