<?php

namespace PayoneApi\Request\Capture;

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
     * @param string|null $referenceId
     *
     * @return \PayoneApi\Request\Capture\Capture|\Api\Request\RequestDataContract
     */
    public static function create($paymentMethod, $data, $referenceId = null)
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
