<?php

namespace PayoneApi\Request;

class Types
{
    const PREAUTHORIZATION = 'preauthorization';
    const AUTHORIZATION = 'authorization';
    const CAPTURE = 'capture';
    const REFUND = 'refund';
    const DEBIT = 'debit';
    const MANAGEMANDATE = 'managemandate';
    const INVOICE = 'getinvoice';

    /**
     * @return mixed
     */
    public static function getRequestTypes()
    {
        $oClass = new \ReflectionClass(__CLASS__);

        return $oClass->getConstants();
    }
}
