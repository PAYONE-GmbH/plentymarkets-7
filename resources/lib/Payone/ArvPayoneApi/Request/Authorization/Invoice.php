<?php

namespace Payone\ArvPayoneApi\Request\Authorization;

use Payone\ArvPayoneApi\Request\AuthorizationRequestAbstract;
use Payone\ArvPayoneApi\Request\ClearingTypes;
use Payone\ArvPayoneApi\Request\GenericAuthorizationRequest;

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
