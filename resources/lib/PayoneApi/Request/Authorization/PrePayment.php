<?php

namespace PayoneApi\Request\Authorization;

use PayoneApi\Request\AuthorizationRequestAbstract;
use PayoneApi\Request\ClearingTypes;
use PayoneApi\Request\GenericAuthorizationRequest;

/**
 * Class PrePayment
 */
class PrePayment extends AuthorizationRequestAbstract
{
    /**
     * @var string
     */
    protected $clearingtype = ClearingTypes::VOR;

    /**
     * Invoice constructor.
     *
     * @param GenericAuthorizationRequest $authorizationRequest
     */
    public function __construct(GenericAuthorizationRequest $authorizationRequest)
    {
        $this->authorizationRequest = $authorizationRequest;
    }
}
