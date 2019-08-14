<?php

namespace PayoneApi\Request\Authorization;

use PayoneApi\Request\AuthorizationRequestAbstract;
use PayoneApi\Request\ClearingTypes;
use PayoneApi\Request\GenericAuthorizationRequest;

/**
 * Class Invoice
 */
class Invoice extends AuthorizationRequestAbstract
{
    protected $clearingtype = ClearingTypes::REC;

    /**
     * Invoice constructor.
     *
     * @param GenericAuthorizationRequest $authorizationRequest
     */
    public function __construct(
        GenericAuthorizationRequest $authorizationRequest
    ) {
        $this->authorizationRequest = $authorizationRequest;
    }
}
