<?php

namespace PayoneApi\Request;

use PayoneApi\Lib\Version;
use PayoneApi\Request\Parts\Config;
use PayoneApi\Request\Parts\SystemInfo;
use PayoneApi\Request\Parts\CartFactory;

class GenericRequestFactory
{
    /**
     * @param $requestType
     * @param $data
     *
     * @return GenericRequest
     */
    public static function create($requestType, $data)
    {

        $context = $data['context'];
        $config = new Config(
            $context['aid'],
            $context['mid'],
            $context['portalid'],
            $context['key'],
            $context['mode']
        );

        $basket = $data['basket'];

        $systemInfoData = $data['systemInfo'];
        $systemInfo = new SystemInfo(
            $systemInfoData['vendor'],
            Version::getVersion(),
            $systemInfoData['module'],
            $systemInfoData['module_version']
        );

        $cart = null;

        if($requestType === Types::PREAUTHORIZATION || $requestType===Types::AUTHORIZATION) {
            $cart = CartFactory::create($data);
        }

        if($requestType === Types::DEBIT || $requestType === Types::REFUND || $requestType === Types::CAPTURE ) {
            $cart = CartFactory::createForRefund($data);
        }
        return new GenericRequest(
            $config,
            $requestType,
            $basket['basketAmount'],
            $basket['currency'],
            $systemInfo,
            $cart,
            $context['sequencenumber'] ?? null

        );
    }
}
