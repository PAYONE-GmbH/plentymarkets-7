<?php

namespace PayoneApi\Request\Managemandate;

use PayoneApi\Lib\Version;
use PayoneApi\Request\Parts\BankAccount;
use PayoneApi\Request\Parts\Config;
use PayoneApi\Request\Parts\Customer;
use PayoneApi\Request\Parts\CustomerAddress;
use PayoneApi\Request\Parts\SystemInfo;
use PayoneApi\Request\RequestFactoryContract;

class ManageMandateRequestFactory implements RequestFactoryContract
{
    /**
     * @param string $paymentMethod
     * @param array $data
     * @param string|null $referenceId
     *
     * @return ManageMandate
     */
    public static function create(string $paymentMethod, array $data, string $referenceId = null)
    {
        $context = $data['context'];
        $config = new Config(
            $context['aid'],
            $context['mid'],
            $context['portalid'],
            $context['key'],
            $context['mode']
        );

        $customerAddressData = $data['shippingAddress'];
        $customerAddress = new CustomerAddress(
            $customerAddressData['street'] . ' ' . $customerAddressData['houseNumber'],
            $customerAddressData['addressaddition'],
            $customerAddressData['postalCode'],
            $customerAddressData['town'],
            $customerAddressData['country']
        );
        $customerData = $data['customer'];
        $customer = new Customer(
            $customerData['title'],
            $customerData['firstname'],
            $customerData['lastname'],
            $customerAddress,
            $customerData['email'],
            $customerData['telephonenumber'],
            $customerData['birthday'],
            $customerData['language'],
            $customerData['gender'],
            $customerData['ip'],
            $customerData['businessrelation'] ?? 'b2c'
        );
        $basket = $data['basket'];

        $systemInfoData = $data['systemInfo'];
        $systemInfo = new SystemInfo(
            $systemInfoData['vendor'],
            Version::getVersion(),
            $systemInfoData['module'],
            $systemInfoData['module_version']
        );
        $bankAccountData = $data['bankAccount'];
        $bankAccount = new BankAccount(
            $bankAccountData['country'],
            $bankAccountData['holder'],
            $bankAccountData['iban'],
            $bankAccountData['bic']
        );

        return new ManageMandate($config, $basket['currency'], $customer, $systemInfo, $bankAccount);
    }
}
