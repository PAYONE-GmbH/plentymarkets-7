<?php

namespace PayoneApi\Request\Debit;

use PayoneApi\Request\GenericRequestFactory;
use PayoneApi\Request\Parts\CartFactory;
use PayoneApi\Request\PaymentTypes;
use PayoneApi\Request\RequestFactoryContract;
use PayoneApi\Request\Types;

class RequestFactory implements RequestFactoryContract
{
    /**
     * @param string $paymentMethod
     * @param array $data
     * @param string $referenceId
     *
     * @return Debit|\PayoneApi\Request\RequestDataContract
     */
    public static function create(string $paymentMethod, array $data, string $referenceId = null)
    {
        $data['basket']['basketAmount'] *= -1;
        $genericRequest = GenericRequestFactory::create(Types::DEBIT, $data);
        $cart = null;
        if ($paymentMethod == PaymentTypes::PAYONE_INVOICE_SECURE) {
            $cart = CartFactory::create($data);
        }
        return new Debit($genericRequest, $referenceId, $cart);
    }
}
