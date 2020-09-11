<?php

namespace Payone\Services;


use Address;
use IO\Builder\Order\AddressType;
use IO\Services\CustomerService;
use function Matrix\add;
use Payone\Adapter\SessionStorage;
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

class AmazonPayService
{
    /** @var Api */
    private $api;

    /** @var GenericPaymentDataProvider */
    private $dataProvider;

    /**
     * AmazonPayService constructor.
     * @param Api $api
     * @param GenericPaymentDataProvider $dataProvider
     */
    public function __construct(Api $api,
                                GenericPaymentDataProvider $dataProvider)
    {
        $this->api = $api;
        $this->dataProvider = $dataProvider;
    }

    public function registerCustomerFromAmazonPay(GetOrderReferenceDetailsResponse $orderRefDetails)
    {

        $addressData['name1'] = $orderRefDetails->getShippingCompany() ?? "";
        $addressData['name2'] = $orderRefDetails->getShippingFirstname() ?? "";
        $addressData['name3'] = $orderRefDetails->getShippingLastname() ?? "";
        $addressData['address1'] = $orderRefDetails->getShippingStreet() ?? "";
        // How to handle this issue?
        $addressData['address2'] = " ";
        $addressData['postalCode'] = $orderRefDetails->getShippingZip() ?? "";
        $addressData['town'] = $orderRefDetails->getShippingCity() ?? "";
        $addressData['countryId'] = $orderRefDetails->getShippingCountry() ?? "";
        $addressData['gender'] = null;


        $newAddress = $this->mapAmazonAddressToAddress($orderRefDetails);

        // Guest or logged-in user?

        /** @var AddressRepositoryContract $contactAddressRepo */
        $addressRepo = pluginApp(AddressRepositoryContract::class);
        $createdAddress = $addressRepo->createAddress($newAddress->toArray());


        return $createdAddress;
    }

    /**
     * @param GetOrderReferenceDetailsResponse $amazonAddress
     * @return Address
     */
    private function mapAmazonAddressToAddress(GetOrderReferenceDetailsResponse $amazonAddress)
    {
        /** @var Address $address */
        $address = pluginApp(\Plenty\Modules\Account\Address\Models\Address::class);

        if (strlen($amazonAddress->getShippingCompany())) {
            $address->name1 = $amazonAddress->getShippingCompany();
            /** @var AddressOption $addressOption */
            $addressOption = pluginApp(\Plenty\Modules\Account\Address\Models\AddressOption::class);

            $addressOption->typeId = AddressOption::TYPE_CONTACT_PERSON;
            $addressOption->value = $amazonAddress->getShippingFirstname()." ".$amazonAddress->getShippingLastname();

            $address->options->push($addressOption->toArray());
        }

        $address->name2 = $amazonAddress->getShippingFirstname();
        $address->name3 = $amazonAddress->getShippingLastname();

        /** @var CountryRepositoryContract $countryContract */
        $countryContract = pluginApp(\Plenty\Modules\Order\Shipping\Countries\Contracts\CountryRepositoryContract::class);

        /** @var Country $country */
        $country = $countryContract->getCountryByIso($amazonAddress->getShippingCountry(), 'isoCode2');

        $addressArr = $this->extractAddress($amazonAddress->getShippingStreet(), '', $country->id == 12); //UK

        $address->address1 = $addressArr[0];
        $address->address2 = $addressArr[1];
        if (strlen($addressArr[2])) {
            $address->address3 = $addressArr[2];
        }
        $address->town = $amazonAddress->getShippingCity();
        $address->postalCode = $amazonAddress->getShippingZip();
        $address->countryId = $country->id;

        if (strlen($amazonAddress->getShippingState())) {
            /** @var CountryState $state */
            $state = $countryContract->getCountryStateByIso($country->id, $amazonAddress->getShippingState());
            $address->state = $state;
            $address->stateId = $state->id;
        }

        if (strlen($amazonAddress->getShippingTelephonenumber())) {
            /** @var AddressOption $addressOption */
            $addressOption = pluginApp(\Plenty\Modules\Account\Address\Models\AddressOption::class);

            $addressOption->typeId = AddressOption::TYPE_TELEPHONE;
            $addressOption->value = $amazonAddress->getShippingTelephonenumber();

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
            "Amazon Pay",
            $workOrderId,
            $amazonReferenceId,
            //   $amazonAddressToken,
            //   $storename,
            $amount
        );

        /** @var SetOrderReferenceDetailsResponse $orderReferenceResponse */
        $orderReferenceResponse = $this->api->doGenericPayment(GenericPayment::ACTIONTYPE_SETORDERREFERENCEDETAILS, $requestParams);
        return $orderReferenceResponse;
    }

    /**
     * @param Basket $basket
     * @return mixed
     */
    public function confirmOrderReference(Basket $basket)
    {
        /** @var SessionStorage $sessionStorage */
        $sessionStorage = pluginApp(SessionStorage::class);

        $workOrderId = $sessionStorage->getSessionValue('workOrderId');
        $amazonReferenceId = $sessionStorage->getSessionValue('amazonReferenceId');
        $amount = $basket->basketAmount;

        $reference = "";

        /** @var GenericPaymentDataProvider $dataProvider */
        $dataProvider = pluginApp(GenericPaymentDataProvider::class);
        /** @var Api $api */
        $api = pluginApp(Api::class);

        $requestParams = $dataProvider->getConfirmOrderReferenceRequestData("Amazon Pay", $workOrderId, $reference, $amazonReferenceId, $amount);

        /** @var ConfirmOrderReferenceResponse $confirmOrderReferenceResponse */
        $confirmOrderReferenceResponse = $api->doGenericPayment(GenericPayment::ACTIONTYPE_CONFIRMORDERREFERENCE, $requestParams);
        return $confirmOrderReferenceResponse;
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
