<?php

namespace PayoneApi\Request\GenericPayment;

use PayoneApi\Lib\Version;
use PayoneApi\Request\Parts\ShippingAddress;
use PayoneApi\Request\Parts\SystemInfo;
use PayoneApi\Request\Parts\Config;
use PayoneApi\Request\RequestFactoryContract;
use PayoneApi\Request\Parts\CartFactory;
use PayoneApi\Request\Parts\Customer;
use PayoneApi\Request\Parts\CustomerAddress;

class GenericPaymentRequestFactory
{
    /**
     * @param string $paymentMethod
     * @param array $data
     * @param string|null $referenceId
     * @return mixed
     */
    public static function create(string $paymentMethod, array $data, string $referenceId = null)
    {
        if($data['context']) {
            $context = $data['context'];
            $config = new Config(
                $context['aid'],
                $context['mid'],
                $context['portalid'],
                $context['key'],
                $context['mode']
            );
        }

        if($data['address']) {
            $shippingAddressData = $data['address'];
            $shippingAddress = new ShippingAddress(
                $shippingAddressData['firstname'],
                $shippingAddressData['lastname'],
                $shippingAddressData['street'],
                '',
                $shippingAddressData['zip'],
                $shippingAddressData['city'],
                $shippingAddressData['country']
            );
        }
        $customerAddressData = $data['address'];
        $customerAddress = new CustomerAddress(
            $customerAddressData['street'] ,
            $customerAddressData['addressaddition']??'',
            $customerAddressData['zip']??'',
            $customerAddressData['city']??'',
            $customerAddressData['country']??''
        );

        $customerData = $data['address'];

        $customer = new Customer(
            $customerData['title']??'',
            $customerData['firstname'],
            $customerData['lastname'],
            $customerAddress,
            $customerData['email'],
            $customerData['telephonenumber']??'',
            $customerData['birthday']??'',
            $customerData['language']??'',
            $customerData['gender']??'',
            $customerData['ip']??'',
            $customerData['businessrelation'] ?? 'b2c'
        );

        if($data['systemInfo']) {
            $systemInfoData = $data['systemInfo'];
            $systemInfo = new SystemInfo(
                $systemInfoData['vendor'],
                Version::getVersion(),
                $systemInfoData['module'],
                $systemInfoData['module_version']
            );
        }

        if($data['add_paydata'] && array_key_exists('action', $data['add_paydata'])) {
            switch ($data['add_paydata']['action']) {
                case 'getconfiguration':
                    // Other configs can be added here. Just add an if-condition for $paymentMethod
                    return new AmazonPayConfigurationRequest($config, $systemInfo, $data['currency']);
                case 'getorderreferencedetails':
                    return new AmazonPayGetOrderReferenceRequest(
                        $config,
                        $systemInfo,
                        $data['add_paydata']['amazon_reference_id'],
                        $data['add_paydata']['amazon_address_token'],
                        $data['workorderid'],
                        $data['amount'],
                        $data['currency']);
                case 'setorderreferencedetails':
                    return new AmazonPaySetOrderReferenceRequest(
                        $config,
                        $systemInfo,
                        $data['add_paydata']['amazon_reference_id'],
                        $data['workorderid'],
                        $data['amount'],
                        $data['currency']);
                case 'confirmorderreference':
                    return new AmazonPayConfirmOrderReferenceRequest(
                        $config,
                        $systemInfo,
                        $data['add_paydata']['amazon_reference_id'],
                        $data['add_paydata']['reference'],
                        $data['workorderid'],
                        $data['amount'],
                        $data['currency'],
                        $data['successurl'],
                        $data['errorurl']);
                case 'start_session':

                    $cart = null;
                    $cart = CartFactory::create($data);

                    return new KlarnaStartSessionRequest(
                        $config,
                        $systemInfo,
                        $data['currency'],
                        $data['amount'],
                        $paymentMethod,
                        $shippingAddress,
                        $data['successurl'],
                        $data['errorurl'],
                        $data['backurl'],
                        $cart,
                        $customer,
                        'cosmin.manciu@plentymarkets.com',
                        'herr',
                        '4930901820'
                    );
            }
        }

        return null;
    }
}
