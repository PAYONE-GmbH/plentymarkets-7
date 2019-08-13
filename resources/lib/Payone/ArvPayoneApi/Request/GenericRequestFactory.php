<?php

namespace Payone\ArvPayoneApi\Request;

use Payone\ArvPayoneApi\Lib\Version;
use Payone\ArvPayoneApi\Request\Parts\Config;
use Payone\ArvPayoneApi\Request\Parts\SystemInfo;

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

        return new GenericRequest(
            $config,
            $requestType,
            $basket['basketAmount'],
            $basket['currency'],
            $systemInfo,
            $context['sequencenumber'] ?? null
        );
    }
}
