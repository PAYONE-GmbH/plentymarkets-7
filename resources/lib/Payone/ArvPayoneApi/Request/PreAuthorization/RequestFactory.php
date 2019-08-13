<?php

namespace Payone\ArvPayoneApi\Request\PreAuthorization;

use Payone\ArvPayoneApi\Request\Authorization\RequestFactory as AuthorizationRequestFactory;
use Payone\ArvPayoneApi\Request\RequestFactoryContract;
use Payone\ArvPayoneApi\Request\Types;

class RequestFactory
    extends AuthorizationRequestFactory
    implements RequestFactoryContract
{
    protected static $requestType = Types::PREAUTHORIZATION;
}
