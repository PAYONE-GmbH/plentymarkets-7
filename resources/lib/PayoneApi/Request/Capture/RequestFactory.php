<?php

namespace PayoneApi\Request\Capture;

use PayoneApi\Request\GenericRequestFactory;
use PayoneApi\Request\Parts\CartFactory;
use PayoneApi\Request\PaymentTypes;
use PayoneApi\Request\RequestFactoryContract;
use PayoneApi\Request\Types;
use PayoneApi\Request\RequestDataContract;

class RequestFactory implements RequestFactoryContract
{
    /**
     * @param string $paymentMethod
     * @param array $data
     * @param string|null $referenceId
     *
     * @return Capture|RequestDataContract
     */
    public static function create(string $paymentMethod, array $data, $referenceId = null)
    {
        $genericRequest = GenericRequestFactory::create(Types::CAPTURE, $data);
        $context = $data['context'];

        $cart = null;
        if ($paymentMethod == PaymentTypes::PAYONE_INVOICE_SECURE) {
            $cart = CartFactory::create($data);
        }

        return new Capture(
            $genericRequest,
            $referenceId,
            $context['capturemode'],
            $context['settleaccount'] ?? SettleAccountModes::AUTO,
            $cart
        );
    }
}
