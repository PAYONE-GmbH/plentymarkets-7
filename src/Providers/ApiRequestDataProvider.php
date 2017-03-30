<?php

namespace Payone\Providers;

use Payone\Helper\PaymentHelper;
use Payone\Methods\PayoneCODPaymentMethod;
use Payone\Services\SessionStorageService;
use Plenty\Modules\Account\Address\Contracts\AddressRepositoryContract;
use Plenty\Modules\Basket\Models\Basket;
use Plenty\Modules\Basket\Models\BasketItem;
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
     * @var SessionStorageService
     */
    private $sessionStorage;

    /**
     * ApiRequestDataProvider constructor.
     * @param PaymentHelper $paymentHelper
     * @param AddressRepositoryContract $addressRepo
     * @param SessionStorageService $sessionStorage
     * @param ItemRepositoryContract $itemRepo
     * @param CountryRepositoryContract $countryRepo
     * @param ShippingServiceProviderRepositoryContract $shippingRepo
     */
    public function __construct(
        PaymentHelper $paymentHelper,
        AddressRepositoryContract $addressRepo,
        SessionStorageService $sessionStorage,
        ItemRepositoryContract $itemRepo,
        CountryRepositoryContract $countryRepo,
        ShippingServiceProviderRepositoryContract $shippingRepo
    ) {
        $this->paymentHelper = $paymentHelper;
        $this->addressRepo = $addressRepo;
        $this->sessionStorage = $sessionStorage;
        $this->itemRepo = $countryRepo;
        $this->countryRepo = $countryRepo;
        $this->shippingInfoRepo = $shippingRepo;
    }

    /**
     * Fill and return the Paypal parameters
     *
     * @param Basket $basket
     *
     * @return array
     */
    public function getPreAuthData(Basket $basket)
    {
        $requestParams = [];
        $paymentCode = PayoneCODPaymentMethod::PAYMENT_CODE;
        $requestParams['context'] = $this->paymentHelper->getApiContextParams($paymentCode);

        /** @var Basket $basket */
        $requestParams['basket'] = $basket;

        $requestParams['basketItems'] = $this->getCartItemData($basket);
        $requestParams['shippingAddress'] = $this->getAddressData(
            $basket->customerShippingAddressId ? $basket->customerShippingAddressId : $basket->customerInvoiceAddressId
        );
        $requestParams['shippingProvider'] = $this->getShippingProviderData($basket->shippingProviderId);
        $requestParams['country'] = $this->getCountryData($basket->shippingCountryId);

        return $requestParams;
    }

    /**
     * @param $addressId
     * @return array
     */
    private function getAddressData($addressId)
    {
        $data = [];

        if (!$addressId) {
            return $data;
        }

        $shippingAddress = $this->addressRepo->findAddressById($addressId);
        $data['town'] = $shippingAddress->town;
        $data['postalCode'] = $shippingAddress->postalCode;
        $data['firstname'] = $shippingAddress->firstName;
        $data['lastname'] = $shippingAddress->lastName;
        $data['street'] = $shippingAddress->street;
        $data['houseNumber'] = $shippingAddress->houseNumber;

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

            $basketItem = $basketItem->getAttributes();

            /** @var ItemText $itemText */
            $itemText = $item->texts;

            $basketItem['name'] = $itemText->first()->name1;

            $items[] = $basketItem;
        }

        return $items;
    }

    /**
     * @param $shippingCountryId
     * @return array
     */
    private function getCountryData($shippingCountryId)
    {
        if (!$shippingCountryId || !$this->countryRepo->findIsoCode($shippingCountryId, 'iso_code_2')) {
            return ['isoCode2' => 'DE'];
        }

        return ['isoCode2' => $this->countryRepo->findIsoCode($shippingCountryId, 'iso_code_2')];

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
        $shippingInfo = $this->shippingInfoRepo->find($providerId);

        return $shippingInfo->toArray();
    }
}
