<?php

namespace PayoneApi\Request\Genericpayment;

use PayoneApi\Lib\Version;
use PayoneApi\Request\Parts\SystemInfo;
use PayoneApi\Request\Parts\Config;
use PayoneApi\Request\RequestFactoryContract;

class GenericPaymentRequestFactory implements RequestFactoryContract
{

    /**
     * @param string $paymentMethod
     * @param array $data
     * @param string|bool $referenceId Reference to previous request
     *
     * @return AmazonPayConfigurationRequest
     */
    public static function create($paymentMethod, $data, $referenceId = null)
    {
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

        switch($data['add_paydata']['action'])
        {
            case 'getconfiguration':
                // Other configs can be added here. Just add an if-condition for $paymentMethod
                return new AmazonPayConfigurationRequest($config, $systemInfo, $data['currency']);
                break;
            case 'getorderreferencedetails':
                //return new AmazonPay
                break;
        }


    }
}
