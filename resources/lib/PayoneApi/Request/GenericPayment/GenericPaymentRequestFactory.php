<?php

namespace PayoneApi\Request\GenericPayment;

use PayoneApi\Lib\Version;
use PayoneApi\Request\Parts\RedirectUrls;
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
     * @return mixed
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

        switch ($data['add_paydata']['action']) {
            case 'getconfiguration':
                // Other configs can be added here. Just add an if-condition for $paymentMethod
                return new AmazonPayConfigurationRequest($config, $systemInfo, $data['currency']);
                break;
            case 'getorderreferencedetails':
                return new AmazonPayGetOrderReferenceRequest(
                    $config,
                    $systemInfo,
                    $data['add_paydata']['amazon_reference_id'],
                    $data['add_paydata']['amazon_address_token'],
                    $data['workorderid'],
                    $data['currency']);
                break;
            case 'setorderreferencedetails':
                return new AmazonPaySetOrderReferenceRequest(
                    $config,
                    $systemInfo,
                    $data['add_paydata']['amazon_reference_id'],
                    $data['workorderid'],
                    $data['amount'],
                    $data['currency']);
                break;
            case 'confirmorderreference':
                return new AmazonPayConfirmOrderReferenceRequest(
                    $config,
                    $systemInfo,
                    $data['add_paydata']['amazon_reference_id'],
                    $data['workorderid'],
                    $data['amount'],
                    $data['currency'],
                    $data['successurl'],
                    $data['errorurl']);
                break;
        }


    }
}
