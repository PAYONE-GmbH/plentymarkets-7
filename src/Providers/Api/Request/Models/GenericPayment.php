<?php

namespace Payone\Providers\Api\Request\Models;


class GenericPayment
{
    const REQUEST_TYPE = "genericpayment";

    const ACTIONTYPE_GETCONFIGURATION = "getconfiguration";
    const ACTIONTYPE_GETORDERREFERENCEDETAILS = "getorderreferencedetails";
    const ACTIONTYPE_SETORDERREFERENCEDETAILS = "setorderreferencedetails";

}
