<?php

namespace Payone\ArvPayoneApi\Request\Managemandate;

use Payone\ArvPayoneApi\Lib\Version;
use Payone\ArvPayoneApi\Request\Parts\BankAccount;
use Payone\ArvPayoneApi\Request\Parts\Config;
use Payone\ArvPayoneApi\Request\Parts\Customer;
use Payone\ArvPayoneApi\Request\Parts\CustomerAddress;
use Payone\ArvPayoneApi\Request\Parts\SystemInfo;
use Payone\ArvPayoneApi\Request\RequestFactoryContract;

class ManageMandateRequestFactory implements RequestFactoryContract
{
    /**
     * @param string $paymentMethod
     * @param array $data
     * @param null $referenceId
     *
     * @return ManageMandate
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
            $customerData['ip']
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
