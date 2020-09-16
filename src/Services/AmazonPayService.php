<?php

namespace Payone\Services;


use Address;
use IO\Builder\Order\AddressType;
use IO\Services\CustomerService;
use function Matrix\add;
use Payone\Adapter\Logger;
use Payone\Adapter\SessionStorage;
use Payone\Methods\PayoneAmazonPayPaymentMethod;
use Payone\Models\Api\GenericPayment\ConfirmOrderReferenceResponse;
use Payone\Models\Api\GenericPayment\GetOrderReferenceDetailsResponse;
use Payone\Models\Api\GenericPayment\SetOrderReferenceDetailsResponse;
use Payone\Providers\Api\Request\GenericPaymentDataProvider;
use Payone\Providers\Api\Request\Models\GenericPayment;
use Plenty\Modules\Account\Address\Contracts\AddressRepositoryContract;
use Plenty\Modules\Account\Address\Models\AddressOption;
use Plenty\Modules\Account\Contact\Contracts\ContactAddressRepositoryContract;
use Plenty\Modules\Basket\Models\Basket;
use Plenty\Modules\Frontend\Services\AccountService;
use Plenty\Modules\Order\Shipping\Countries\Contracts\CountryRepositoryContract;
use Plenty\Modules\Order\Shipping\Countries\Models\Country;
use Plenty\Modules\Order\Shipping\Countries\Models\CountryState;
use Plenty\Plugin\Log\Loggable;

class AmazonPayService
{
    /** @var Api */
    private $api;

    /** @var GenericPaymentDataProvider */
    private $dataProvider;

    /** @var Logger  */
    private $logger;

    /**
     * AmazonPayService constructor.
     * @param Api $api
     * @param GenericPaymentDataProvider $dataProvider
     * @param Logger $logger
     */
    public function __construct(Api $api,
                                GenericPaymentDataProvider $dataProvider,
                                Logger $logger)
    {
        $this->api = $api;
        $this->dataProvider = $dataProvider;
        $this->logger = $logger;
    }

    public function registerCustomerFromAmazonPay(GetOrderReferenceDetailsResponse $orderRefDetails, $billingAddress = false)
    {
        $this->logger
            ->setIdentifier(__METHOD__)
            ->debug('AmazonPay.registerCustomer', (array)$orderRefDetails);

        $addressData = [];
        if ($billingAddress)
        {
            $addressData['company'] = $orderRefDetails->getBillingCompany() ?? "";
            $addressData['firstName'] = $orderRefDetails->getBillingFirstname() ?? "";
            $addressData['lastName'] = $orderRefDetails->getBillingLastname() ?? "";
            $addressData['streetAndNumber'] = $orderRefDetails->getBillingStreet() ?? "";
            $addressData['postalCode'] = $orderRefDetails->getBillingZip() ?? "";
            $addressData['city'] = $orderRefDetails->getBillingCity() ?? "";
            $addressData['state'] = $orderRefDetails->getBillingState() ?? "";
            $addressData['countryId'] = $orderRefDetails->getBillingCountry() ?? "";
            $addressData['telNo'] = $orderRefDetails->getBillingTelephonenumber() ?? "";
        } else {
            $addressData['company'] = $orderRefDetails->getShippingCompany() ?? "";
            $addressData['firstName'] = $orderRefDetails->getShippingFirstname() ?? "";
            $addressData['lastName'] = $orderRefDetails->getShippingLastname() ?? "";
            $addressData['streetAndNumber'] = $orderRefDetails->getShippingStreet() ?? "";
            $addressData['postalCode'] = $orderRefDetails->getShippingZip() ?? "";
            $addressData['city'] = $orderRefDetails->getShippingCity() ?? "";
            $addressData['state'] = $orderRefDetails->getShippingState() ?? "";
            $addressData['countryId'] = $orderRefDetails->getShippingCountry() ?? "";
            $addressData['telNo'] = $orderRefDetails->getShippingTelephonenumber() ?? "";
        }

        $newAddress = $this->mapAmazonAddressToAddress($addressData);

        /** @var AddressRepositoryContract $contactAddressRepo */
        $addressRepo = pluginApp(AddressRepositoryContract::class);
        $createdAddress = $addressRepo->createAddress($newAddress->toArray());

        return $createdAddress;
    }



    /**
     * @param GetOrderReferenceDetailsResponse $amazonAddress
     * @return Address
     */
    private function mapAmazonAddressToAddress(array $amazonAddress)
    {
        /** @var Address $address */
        $address = pluginApp(\Plenty\Modules\Account\Address\Models\Address::class);

        if (strlen($amazonAddress['company'])) {
            $address->name1 = $amazonAddress['company'];
            /** @var AddressOption $addressOption */
            $addressOption = pluginApp(\Plenty\Modules\Account\Address\Models\AddressOption::class);

            $addressOption->typeId = AddressOption::TYPE_CONTACT_PERSON;
            $addressOption->value = $amazonAddress['firstName']." ".$amazonAddress['lastName'];

            $address->options->push($addressOption->toArray());
        }

        $address->name2 = $amazonAddress['firstName'];
        $address->name3 = $amazonAddress['lastName'];

        /** @var CountryRepositoryContract $countryContract */
        $countryContract = pluginApp(\Plenty\Modules\Order\Shipping\Countries\Contracts\CountryRepositoryContract::class);

        /** @var Country $country */
        $country = $countryContract->getCountryByIso($amazonAddress['countryId'], 'isoCode2');

        $addressArr = $this->extractAddress($amazonAddress['streetAndNumber'], '', $country->id == 12); //UK

        $address->address1 = $addressArr[0];
        $address->address2 = $addressArr[1];
        if (strlen($addressArr[2])) {
            $address->address3 = $addressArr[2];
        }
        $address->town = $amazonAddress['city'];
        $address->postalCode = $amazonAddress['postalCode'];
        $address->countryId = $country->id;

        if (strlen($amazonAddress['state'])) {
            /** @var CountryState $state */
            $state = $countryContract->getCountryStateByIso($country->id, $amazonAddress['state']);
            $address->state = $state;
            $address->stateId = $state->id;
        }

        if (strlen($amazonAddress['telNo'])) {
            /** @var AddressOption $addressOption */
            $addressOption = pluginApp(\Plenty\Modules\Account\Address\Models\AddressOption::class);

            $addressOption->typeId = AddressOption::TYPE_TELEPHONE;
            $addressOption->value = $amazonAddress['telNo'];

            $address->options->push($addressOption->toArray());
        }

        return $address;
    }

    /**
     * @param Basket $basket
     * @return mixed
     */
    public function setOrderReference(Basket $basket)
    {
        /** @var SessionStorage $sessionStorage */
        $sessionStorage = pluginApp(SessionStorage::class);
        $amount = $basket->basketAmount;
        $workOrderId = $sessionStorage->getSessionValue('workOrderId');
        $amazonReferenceId = $sessionStorage->getSessionValue('amazonReferenceId');

        $requestParams = $this->dataProvider->getSetOrderReferenceDetailsRequestData(
            PayoneAmazonPayPaymentMethod::PAYMENT_CODE,
            $workOrderId,
            $amazonReferenceId,
            //   $amazonAddressToken,
            //   $storename,
            $amount
        );

        /** @var SetOrderReferenceDetailsResponse $orderReferenceResponse */
        $orderReferenceResponse = $this->api->doGenericPayment(GenericPayment::ACTIONTYPE_SETORDERREFERENCEDETAILS, $requestParams);

        $this->logger
            ->setIdentifier(__METHOD__)
            ->debug('AmazonPay.setOrderReference', [
                "workOrderId" => $workOrderId,
                "amazonReferenceId" => $amazonReferenceId,
                "requestParams" => $requestParams,
                "setOrderReferenceResponse" => (array)$orderReferenceResponse
            ]);

        return $orderReferenceResponse;
    }

    /**
     * @param Basket $basket
     * @return mixed
     */
    public function confirmOrderReference(Basket $basket)
    {
        try {


            /** @var SessionStorage $sessionStorage */
            $sessionStorage = pluginApp(SessionStorage::class);

            $workOrderId = $sessionStorage->getSessionValue('workOrderId');
            $amazonReferenceId = $sessionStorage->getSessionValue('amazonReferenceId');
            $amount = $basket->basketAmount;

            $reference = $basket->orderId;

            $requestParams = $this->dataProvider->getConfirmOrderReferenceRequestData(
                PayoneAmazonPayPaymentMethod::PAYMENT_CODE,
                $workOrderId,
                $reference,
                $amazonReferenceId,
                $amount
            );

            /** @var ConfirmOrderReferenceResponse $confirmOrderReferenceResponse */
            $confirmOrderReferenceResponse = $this->api->doGenericPayment(GenericPayment::ACTIONTYPE_CONFIRMORDERREFERENCE, $requestParams);

            $this->logger
                ->setIdentifier(__METHOD__)
                ->debug('AmazonPay.confirmOrderReference', [
                    "workOrderId" => $workOrderId,
                    "amazonReferenceId" => $amazonReferenceId,
                    "amount" => $amount,
                    "requestParams" => $requestParams,
                    "confirmOrderReferenceResponse" => (array)$confirmOrderReferenceResponse
                ]);

            return $confirmOrderReferenceResponse;
        } catch (\Exception $exception)
        {
            $this->logger
                ->setIdentifier(__METHOD__)
                ->error('AmazonPay.confirmOrderReference', $exception);

            return $exception;
        }
    }


    /**
     * extract the house number, the street and the additional name from the specified address fields
     * @param string $street1
     * @param string $street2
     * @param bool $checkUKAddress
     * @return array (street, houseNo, additionalAddress)
     */
    private function extractAddress(String $street1, String $street2, $checkUKAddress=false)
    {
        $address = trim($street1 . ' ' . $street2);

        $reqex = '/(?<ad>(.*?)[\D]{3}[\s,.])(?<no>';
        $reqex .= '|[0-9]{1,3}[ a-zA-Z-\/\.]{0,6}'; // f.e. "Rosenstr. 14"
        $reqex .= '|[0-9]{1,3}[ a-zA-Z-\/\.]{1,6}[0-9]{1,4}[ a-zA-Z-\/\.]{0,6}[0-9]{0,3}[ a-zA-Z-\/\.]{0,6}[0-9]{0,3}'; // f.e. "Straße in Österreich 30/4/12.2"
        $reqex .= ')$/';
        $reqex4foreign = '/^(?<no>[0-9]{1,4}([\D]{0,2}([\s]|[^a-zA-Z0-9])))(?<ad>([\D]+))$/';    // f.e. "16 Bellevue Road"
        if (stripos($address, 'POSTFILIALE') !== false) {
            $id = '';
            $result = array();

            if (preg_match("/([\D].*?)(([\d]{4,})|(?<id>[\d]{3}))([\D]*?)/i", $address, $result) > 0) {
                $id = $result['id'];

                $address = preg_replace("/([\D].*?)" . $result['id'] . "([\D]*)/i", '\1\2', $address);

                if ($id
                    && preg_match("/(?<id>[\d\s]{6,14})/i", $address, $result) > 0) {
                    $street = preg_replace("/\s/", '', $result['id']) . ' POSTFILIALE';
                    $houseNo = $id;
                    $additionalAddress = '';

                    return array(
                        $street,
                        $houseNo,
                        $additionalAddress,
                    );
                }
            }
        }

        if($checkUKAddress && preg_match($reqex4foreign, $street1, $machtes) > 0) {
            $street         = trim($machtes['ad']);
            $houseNo        = trim($machtes['no']);
            $additionalAddress = $street2;
        } elseif (preg_match($reqex4foreign, $street1, $matches) > 0) {
            // house number is in street1 - foreign address
            $street = trim($matches['no']) . ' ' . trim($matches['ad']);
            $houseNo = '';
            $additionalAddress = $street2;
        } else {
            if (preg_match($reqex, $street1, $matches) > 0) {
                // house number is in street1
                $street = trim($matches['ad']);
                $houseNo = trim($matches['no']);
                $additionalAddress = $street2;
            } else {
                if (preg_match($reqex4foreign, $street2, $matches) > 0) {
                    // house number is in street2 - foreign address
                    $street = trim($matches['no']) . ' ' . trim($matches['ad']);
                    $houseNo = '';
                    $additionalAddress = $street1;
                } else {
                    if (preg_match($reqex, $street2, $matches) > 0) {
                        // house number is in street2
                        $street = trim($matches['ad']);
                        $houseNo = trim($matches['no']);
                        $additionalAddress = $street1;
                    } else {
                        if (preg_match($reqex, $address, $matches) > 0) {
                            // house number is in street2
                            $street = trim($matches['ad']);
                            $houseNo = trim($matches['no']);
                            $additionalAddress = '';
                        } else {
                            // no house number was found
                            $street = $address;
                            $houseNo = '';
                            $additionalAddress = '';
                        }
                    }
                }
            }
        }

        return array(
            $street,
            $houseNo,
            $additionalAddress,
        );
    }
}
