<?php

namespace PayoneApi\Request;

interface RequestFactoryContract
{
    /**
     * @param string $paymentMethod
     * @param array $data
     * @param string|bool $referenceId Reference to implements RequestFactoryContractprevious request
     *
     * @return RequestDataContract
     */
    public static function create(string $paymentMethod, array $data, string $referenceId = null);
}
