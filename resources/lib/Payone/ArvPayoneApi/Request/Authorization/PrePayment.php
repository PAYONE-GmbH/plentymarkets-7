<?php

namespace Payone\ArvPayoneApi\Request\Authorization;

use Payone\ArvPayoneApi\Request\AuthorizationRequestAbstract;
use Payone\ArvPayoneApi\Request\ClearingTypes;
use Payone\ArvPayoneApi\Request\GenericAuthorizationRequest;

/**
 * Class PrePayment
 */
class PrePayment extends AuthorizationRequestAbstract
{
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
