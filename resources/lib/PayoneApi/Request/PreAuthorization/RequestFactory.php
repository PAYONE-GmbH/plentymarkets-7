<?php

namespace PayoneApi\Request\PreAuthorization;

use PayoneApi\Request\Authorization\RequestFactory as AuthorizationRequestFactory;
use PayoneApi\Request\RequestFactoryContract;
use PayoneApi\Request\Types;

class RequestFactory
    extends AuthorizationRequestFactory
    implements RequestFactoryContract
{
    protected static $requestType = Types::PREAUTHORIZATION;
}
