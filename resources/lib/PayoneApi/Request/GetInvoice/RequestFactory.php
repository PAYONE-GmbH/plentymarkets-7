<?php

namespace PayoneApi\Request\GetInvoice;

use PayoneApi\Lib\Version;
use PayoneApi\Request\Parts\Config;
use PayoneApi\Request\Parts\SystemInfo;
use PayoneApi\Request\PaymentTypes;
use PayoneApi\Request\RequestFactoryContract;

class RequestFactory implements RequestFactoryContract
{
    /**
     * @param string $paymentMethod
     * @param array $data
     * @param string|null $referenceId
     *
     * @return GetInvoice
     */
    public static function create(string $paymentMethod, array $data, string $referenceId = null)
    {
        if ($paymentMethod != PaymentTypes::PAYONE_INVOICE_SECURE) {
            throw new \Exception('Get invoice only for secure invoice');
        }
        $context = $data['context'];
        $config = new Config(
            $context['aid'],
            $context['mid'],
            $context['portalid'],
            $context['key'],
            $context['mode']
        );

        $systemInfoData = $data['systemInfo'];
        $systemInfo = new SystemInfo(
            $systemInfoData['vendor'],
            Version::getVersion(),
            $systemInfoData['module'],
            $systemInfoData['module_version']
        );

        return new GetInvoice($config,
            $context['documentNumber'],
            $systemInfo);
    }
}
