<?php

namespace Payone\ArvPayoneApi\Request\Authorization;

use Payone\ArvPayoneApi\Request\AuthorizationRequestAbstract;
use Payone\ArvPayoneApi\Request\GenericAuthRequestFactory;
use Payone\ArvPayoneApi\Request\Parts\BankAccount;
use Payone\ArvPayoneApi\Request\Parts\CartFactory;
use Payone\ArvPayoneApi\Request\Parts\RedirectUrls;
use Payone\ArvPayoneApi\Request\Parts\SepaMandate;
use Payone\ArvPayoneApi\Request\Parts\ShippingAddress;
use Payone\ArvPayoneApi\Request\PaymentTypes;
use Payone\ArvPayoneApi\Request\RequestFactoryContract;
use Payone\ArvPayoneApi\Request\Types;

class RequestFactory implements RequestFactoryContract
{
    protected static $requestType = Types::AUTHORIZATION;

    /**
     * @param string $paymentMethod
     * @param array $data
     * @param bool $referenceId
     *
     * @throws \Exception
     *
     * @return AuthorizationRequestAbstract
     */
    public static function create($paymentMethod, $data, $referenceId = null)
    {
        $genericAuthRequest = GenericAuthRequestFactory::create(static::$requestType, $data);
        switch ($paymentMethod) {
            case PaymentTypes::PAYONE_INVOICE:
                return new Invoice($genericAuthRequest);
            case PaymentTypes::PAYONE_INVOICE_SECURE:
                return new InvoiceSecure($genericAuthRequest, CartFactory::create($data));
            case PaymentTypes::PAYONE_PRE_PAYMENT:
                return new PrePayment($genericAuthRequest);
            case PaymentTypes::PAYONE_CASH_ON_DELIVERY:
                return new CashOnDelivery(
                    $genericAuthRequest,
                    $data['shippingProvider']['name']
                );
            case PaymentTypes::PAYONE_CREDIT_CARD:

                return new Creditcard(
                    $genericAuthRequest,
                    self::createUrls($data['redirect']),
                    $data['pseudocardpan']
                );
            case PaymentTypes::PAYONE_DIRECT_DEBIT:
                $sepaMandateData = $data['sepaMandate'];
                $sepaMandate = new SepaMandate(
                    $sepaMandateData['identification'],
                    $sepaMandateData['dateofsignature'],
                    $sepaMandateData['iban'],
                    $sepaMandateData['bic'],
                    $sepaMandateData['bankcountry']
                );

                return new DirectDebit(
                    $genericAuthRequest,
                    $sepaMandate
                );
            case PaymentTypes::PAYONE_PAY_PAL:

                return new PayPal(
                    $genericAuthRequest,
                    self::createUrls($data['redirect'])
                );
            case PaymentTypes::PAYONE_SOFORT:
                $bankAccountData = $data['bankAccount'];
                $bankAccount = new BankAccount(
                    $bankAccountData['country'],
                    $bankAccountData['holder'],
                    $bankAccountData['iban'],
                    $bankAccountData['bic']
                );

                return new Sofort(
                    $genericAuthRequest,
                    self::createUrls($data['redirect']),
                    $bankAccount
                );
            case PaymentTypes::PAYONE_ON_LINE_BANK_TRANSFER:
                $bankAccountData = $data['bankAccount'];
                $bankAccount = new BankAccount(
                    $bankAccountData['country'],
                    $bankAccountData['holder'],
                    $bankAccountData['iban'],
                    $bankAccountData['bic']
                );

                return new OnlineBankTransfer(
                    $genericAuthRequest,
                    self::createUrls($data['redirect']),
                    $bankAccount
                );

            case PaymentTypes::PAYONE_PAYDIREKT:
                $customerAddressData = $data['shippingAddress'];
                $shippingAddress = new ShippingAddress(
                    $customerAddressData['firstname'],
                    $customerAddressData['lastname'],
                    $customerAddressData['street'],
                    $customerAddressData['addressaddition'],
                    $customerAddressData['postalCode'],
                    $customerAddressData['town'],
                    $customerAddressData['country']
                );

                return new Paydirekt(
                    $genericAuthRequest,
                    self::createUrls($data['redirect']),
                    $shippingAddress
                );
        }
        throw new \Exception('Unimplemented payment method ' . $paymentMethod);
    }

    /**
     * @param $redirectData
     *
     * @return RedirectUrls
     */
    private static function createUrls($redirectData)
    {
        return new RedirectUrls($redirectData['success'], $redirectData['error'], $redirectData['back']);
    }
}
