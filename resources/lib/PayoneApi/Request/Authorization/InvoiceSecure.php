<?php

namespace PayoneApi\Request\Authorization;

use PayoneApi\Request\AuthorizationRequestAbstract;
use PayoneApi\Request\ClearingTypes;
use PayoneApi\Request\GenericAuthorizationRequest;
use PayoneApi\Request\Parts\Cart;

/**
 * Class InvoiceSecure
 */
class InvoiceSecure extends AuthorizationRequestAbstract
{
    const PAYONE_INVOICE_CLEARING_TYPE = 'POV';

    protected $clearingtype = ClearingTypes::REC;

    protected $subclearingtype = self::PAYONE_INVOICE_CLEARING_TYPE;
    /**
     * @var Cart
     */
    private $cart;

    /**
     * Invoice constructor.
     *
     * @param GenericAuthorizationRequest $authorizationRequest
     * @param Cart $cart
     */
    public function __construct(
        GenericAuthorizationRequest $authorizationRequest,
        Cart $cart
    ) {
        $this->authorizationRequest = $authorizationRequest;
        $this->cart = $cart;
    }

    /**
     * Getter for Subclearingtype
     *
     * @return string
     */
    public function getSubclearingtype()
    {
        return $this->subclearingtype;
    }

    /**
     * Getter for Cart
     * @return Cart
     */
    public function getCart()
    {
        return $this->cart;
    }
}
