<?php

namespace Payone\ArvPayoneApi\Request\Capture;

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
     * @param string|null $referenceId
     *
     * @return \Payone\ArvPayoneApi\Request\Capture\Capture|\ArvPayoneApi\Request\RequestDataContract
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
