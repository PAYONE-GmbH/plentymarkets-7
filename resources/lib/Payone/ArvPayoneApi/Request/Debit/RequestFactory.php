<?php

namespace Payone\ArvPayoneApi\Request\Debit;

use Payone\ArvPayoneApi\Request\GenericRequestFactory;
use Payone\ArvPayoneApi\Request\Parts\CartFactory;
use Payone\ArvPayoneApi\Request\PaymentTypes;
use Payone\ArvPayoneApi\Request\RequestFactoryContract;
use Payone\ArvPayoneApi\Request\Types;

class RequestFactory implements RequestFactoryContract
{
    /**
     * @param string $paymentMethod
     * @param array $data
     * @param null $referenceId
     *
     * @return Debit|\Payone\ArvPayoneApi\Request\RequestDataContract
     */
    public static function create($paymentMethod, $data, $referenceId = null)
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
