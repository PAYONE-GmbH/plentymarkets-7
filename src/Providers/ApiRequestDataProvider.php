<?php



namespace Payone\Providers;

use Payone\Helper\PaymentHelper;
use Payone\Methods\PayonePayolutionInstallmentPaymentMethod;
use Payone\Methods\PayoneRatePayInstallmentPaymentMethod;
use Plenty\Modules\Account\Address\Contracts\AddressRepositoryContract;
use Plenty\Modules\Account\Contact\Contracts\ContactRepositoryContract;
use Plenty\Modules\Account\Contact\Models\Contact;
use Plenty\Modules\Basket\Models\Basket;
use Plenty\Modules\Basket\Models\BasketItem;
use Plenty\Modules\Frontend\Services\AccountService;
use Plenty\Modules\Frontend\Session\Storage\Contracts\FrontendSessionStorageFactoryContract;
use Plenty\Modules\Item\Item\Contracts\ItemRepositoryContract;
use Plenty\Modules\Item\Item\Models\Item;
use Plenty\Modules\Item\Item\Models\ItemText;
use Plenty\Modules\Order\Shipping\Countries\Contracts\CountryRepositoryContract;
use Plenty\Modules\Order\Shipping\ServiceProvider\Contracts\ShippingServiceProviderRepositoryContract;

/**
 * Class ApiRequestDataProvider
 */
class ApiRequestDataProvider
{
    /**
     * @var ContactRepositoryContract
     */
    private $contactRepositoryContract;

    /**
     * @var CountryRepositoryContract
     */
    private $itemRepo;

    /**
     * @var CountryRepositoryContract
     */
    private $countryRepo;

    /**
     * @var ShippingServiceProviderRepositoryContract
     */
    private $shippingInfoRepo;

    /**
     * @var PaymentHelper
     */
    private $paymentHelper;

    /**
     * @var AddressRepositoryContract
     */
    private $addressRepo;

    /**
     * @var AccountService
     */
    private $accountService;

    /**
     * @var FrontendSessionStorageFactoryContract
     */
    private $sessionStorageFactory;

    /**
     * ApiRequestDataProvider constructor.
     *
     * @param PaymentHelper $paymentHelper
     * @param AddressRepositoryContract $addressRepo
     * @param ItemRepositoryContract $itemRepo
     * @param CountryRepositoryContract $countryRepo
     * @param ShippingServiceProviderRepositoryContract $shippingRepo
     * @param AccountService $accountService
     * @param ContactRepositoryContract $contactRepositoryContract
     */
    public function __construct(
        PaymentHelper $paymentHelper,
        AddressRepositoryContract $addressRepo,
        ItemRepositoryContract $itemRepo,
        CountryRepositoryContract $countryRepo,
        ShippingServiceProviderRepositoryContract $shippingRepo,
        AccountService $accountService,
        ContactRepositoryContract $contactRepositoryContract,
        FrontendSessionStorageFactoryContract $sessionStorageFactory
    ) {
        $this->paymentHelper = $paymentHelper;
        $this->addressRepo = $addressRepo;
        $this->itemRepo = $itemRepo;
        $this->countryRepo = $countryRepo;
        $this->shippingInfoRepo = $shippingRepo;
        $this->accountService = $accountService;
        $this->accountService = $accountService;
        $this->contactRepositoryContract = $contactRepositoryContract;
        $this->sessionStorageFactory = $sessionStorageFactory;
    }

    /**
     * Fill and return the Paypal parameters
     *
     * @param string $paymentCode
     * @param Basket $basket
     *
     * @return array
     */
    public function getPreAuthData($paymentCode, Basket $basket)
    {
        $requestParams['context'] = $this->paymentHelper->getApiContextParams($paymentCode);

        /** @var Basket $basket */
        $requestParams['basket'] = $basket->toArray();
        $requestParams['basket']['grandTotal'] = $basket->basketAmount;
        $requestParams['basket']['cartId'] = $basket->id;

        $requestParams['basketItems'] = $this->getCartItemData($basket);
        $requestParams['shippingAddress'] = $this->getAddressData(
            $basket->customerShippingAddressId ? $basket->customerShippingAddressId : $basket->customerInvoiceAddressId
        );
        $requestParams['billingAddress'] = $this->getAddressData(
            $basket->customerInvoiceAddressId
        );
        $requestParams['shippingProvider'] = $this->getShippingProviderData($basket->shippingProviderId);
        $requestParams['country'] = $this->getCountryData($basket->shippingCountryId);
        $requestParams['customer'] = $this->getCustomerData();
        if ($paymentCode == PayonePayolutionInstallmentPaymentMethod::PAYMENT_CODE ||
            $paymentCode == PayoneRatePayInstallmentPaymentMethod::PAYMENT_CODE
        ) {
            $requestParams['installment'] = $this->getInstallmentData();
        }

        $requestParams['account'] = [];
        // TODO: Retrieve Account data per payment $this->getAccountData();

        return $requestParams;
    }

    /**
     * @return array
     */
    public function getInstallmentData()
    {
        //TODO
        return [
            'calculationId' => 'Tx-....', // UniqueID
            'amount' => '359.98',
            'durationInMonth' => 6,
        ];
    }

    /**
     * @return array
     */
    public function getAccountData()
    {
        //TODO
        return [
            'holder' => 'Max Mustermann',
            'country' => 'AT',
            'bic' => 'GIBAATWW',
            'iban' => 'AT622011198765432123',
        ];
    }

    /**
     * @param $addressId
     *
     * @return array
     */
    private function getAddressData($addressId)
    {
        $data = [];

        if (!$addressId) {
            return $data;
        }

        try {
            $address = $this->addressRepo->findAddressById($addressId);
        } catch (\Exception $e) {
            // Maybe not logged in anymore?
            return $data;
        }
        $data = $address->toArray();
        $data['town'] = $address->town;
        $data['postalCode'] = $address->postalCode;
        $data['firstname'] = $address->firstName;
        $data['lastname'] = $address->lastName;
        $data['street'] = $address->street;
        $data['houseNumber'] = $address->houseNumber;
        $data['country'] = $address->country;
        $data['addressaddition'] = $address->additional;


        return $data;
    }

    /**
     * @param Basket $basket
     *
     * @return array
     */
    private function getCartItemData(Basket $basket)
    {
        $items = [];

        if (!$basket->basketItems) {
            return $items;
        }
        /** @var BasketItem $basketItem */
        foreach ($basket->basketItems as $basketItem) {
            /** @var Item $item */
            $item = $this->itemRepo->show($basketItem->itemId);
            /** @var ItemText $itemText */
            $itemText = $item->texts;

            $basketItem = $basketItem->toArray();
            $basketItem['tax'] = sprintf(
                '%01.2f',
                $basketItem['price'] - $basketItem['price'] * 100 / ($basketItem['vat'] + 100.));
            $basketItem['name'] = $itemText->first()->name1;

            $items[] = $basketItem;
        }

        return $items;
    }

    /**
     * @param $countryId
     *
     * @return string iso_code_2 country code
     */
    private function getCountryData($countryId)
    {
        if (!$countryId || !$this->countryRepo->findIsoCode($countryId, 'iso_code_2')) {
            return 'DE';
        }

        return $this->countryRepo->findIsoCode($countryId, 'iso_code_2');
    }

    /**
     * @param int $providerId
     *
     * @return array
     */
    private function getShippingProviderData($providerId)
    {
        if (!$providerId) {
            return [];
        }
        try {
            $shippingInfo = $this->shippingInfoRepo->find($providerId);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return [];
        }

        return $shippingInfo->toArray();
    }

    private function getCustomerData()
    {
        $customer = $this->sessionStorageFactory->getCustomer()->toArray();

        if (!$this->accountService->getIsAccountLoggedIn()) {
            //TODO: Load Guest data
        }

        $contactId = $this->accountService->getAccountContactId();
        if (!$contactId) {
            return $customer;
        }
        try {
            /** @var Contact $contact */
            $contact = $this->contactRepositoryContract->findContactById($contactId);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $customer;
        }
        $customer = $customer + $contact->toArray();
        $customer['email'] = $contact->email;
        $customer['ip'] = '127.0.0.1';
        $customer['firstname'] = $contact->firstName;
        $customer['lastname'] = $contact->lastName;
        //TODO: Check format
        $customer['gender'] = ((string)$contact->gender)[0];
        $customer['birthday'] = $contact->birthdayAt;
        $customer['title'] = '';//$contact->AdditionalName;
        $customer['telephonenumber'] = $contact->privatePhone;
        $customer['language'] = $contact->lang;

        return $customer;
    }
}
