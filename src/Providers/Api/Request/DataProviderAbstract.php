<?php

namespace Payone\Providers\Api\Request;

use Payone\Adapter\Config as ConfigAdapter;
use Payone\Adapter\SessionStorage;
use Payone\Helpers\AddressHelper;
use Payone\Helpers\PaymentHelper;
use Payone\Helpers\ShopHelper;
use Payone\Services\RequestDataValidator;
use Plenty\Modules\Account\Address\Contracts\AddressRepositoryContract;
use Plenty\Modules\Account\Address\Models\Address;
use Plenty\Modules\Account\Contact\Contracts\ContactRepositoryContract;
use Plenty\Modules\Account\Contracts\AccountRepositoryContract;
use Plenty\Modules\Basket\Models\Basket;
use Plenty\Modules\Basket\Models\BasketItem;
use Plenty\Modules\Frontend\Session\Storage\Contracts\FrontendSessionStorageFactoryContract;
use Plenty\Modules\Item\Item\Contracts\ItemRepositoryContract;
use Plenty\Modules\Item\Item\Models\Item;
use Plenty\Modules\Item\Item\Models\ItemText;
use Plenty\Modules\Order\Models\Order;
use Plenty\Modules\Order\Models\OrderItem;
use Plenty\Modules\Order\Shipping\Countries\Contracts\CountryRepositoryContract;
use Plenty\Modules\Order\Shipping\Information\Contracts\ShippingInformationRepositoryContract;

/**
 * Class DataProviderAbstract
 */
abstract class DataProviderAbstract
{
    const ACCOUNT_DATA_KEY = 'paymentAccount';

    /**
     * @var ContactRepositoryContract
     */
    protected $contactRepo;

    /**
     * @var ItemRepositoryContract
     */
    protected $itemRepo;

    /**
     * @var CountryRepositoryContract
     */
    protected $countryRepo;

    /**
     * @var ShippingInformationRepositoryContract
     */
    protected $shippingProviderRepository;

    /**
     * @var PaymentHelper
     */
    protected $paymentHelper;

    /**
     * @var AddressRepositoryContract
     */
    protected $addressRepo;

    /**
     * @var SessionStorage
     */
    protected $sessionStorage;

    /**
     * @var FrontendSessionStorageFactoryContract
     */
    protected $sessionStorageFactory;
    /**
     * @var AccountRepositoryContract
     */
    protected $accountRepositoryContract;
    /**
     * @var AddressHelper
     */
    protected $addressHelper;
    /**
     * @var ShopHelper
     */
    protected $shopHelper;
    /**
     * @var ConfigAdapter
     */
    protected $config;
    /**
     * @var RequestDataValidator
     */
    protected $validator;

    /**
     * DataProviderAbstract constructor.
     * @param PaymentHelper $paymentHelper
     * @param AddressRepositoryContract $addressRepo
     * @param SessionStorage $sessionStorage
     * @param ItemRepositoryContract $itemRepo
     * @param CountryRepositoryContract $countryRepo
     * @param ShippingInformationRepositoryContract $shippingRepo
     * @param ContactRepositoryContract $contactRepositoryContract
     * @param FrontendSessionStorageFactoryContract $sessionStorageFactory
     * @param AccountRepositoryContract $accountRepositoryContract
     * @param ShopHelper $shopHelper
     * @param AddressHelper $addressHelper
     * @param ConfigAdapter $config
     * @param RequestDataValidator $validator
     */
    public function __construct(
        PaymentHelper $paymentHelper,
        AddressRepositoryContract $addressRepo,
        SessionStorage $sessionStorage,
        ItemRepositoryContract $itemRepo,
        CountryRepositoryContract $countryRepo,
        ShippingInformationRepositoryContract $shippingRepo,
        ContactRepositoryContract $contactRepositoryContract,
        FrontendSessionStorageFactoryContract $sessionStorageFactory,
        AccountRepositoryContract $accountRepositoryContract,
        ShopHelper $shopHelper,
        AddressHelper $addressHelper,
        ConfigAdapter $config,
        RequestDataValidator $validator
    ) {
        $this->paymentHelper = $paymentHelper;
        $this->addressRepo = $addressRepo;
        $this->sessionStorage = $sessionStorage;
        $this->itemRepo = $itemRepo;
        $this->countryRepo = $countryRepo;
        $this->shippingProviderRepository = $shippingRepo;
        $this->contactRepo = $contactRepositoryContract;
        $this->sessionStorageFactory = $sessionStorageFactory;
        $this->accountRepositoryContract = $accountRepositoryContract;
        $this->shopHelper = $shopHelper;
        $this->addressHelper = $addressHelper;
        $this->config = $config;
        $this->validator = $validator;
    }

    /**
     * @return array
     */
    protected function getInstallmentData()
    {
        return [];
    }

    /**
     * @return array
     */
    protected function getAccountData()
    {
        $account = $this->sessionStorage->getSessionValue(self::ACCOUNT_DATA_KEY);

        if (!($account instanceof BankAccount)) {
            return [];
        }

        return [
            'holder' => $account->getHolder(),
            'country' => $account->getCountryCode(),
            'bic' => $account->getBic(),
            'iban' => $account->getIban(),
        ];
    }

    /**
     * @param string $paymentCode
     *
     * @return bool
     */
    protected function paymentHasAccount(string $paymentCode): bool
    {
        return in_array($paymentCode, []);
    }

    /**
     * @param Basket $basket
     *
     * @return array
     */
    protected function getCartItemData(Basket $basket)
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
     * @param Order $order
     *
     * @return array
     */
    protected function getOrderItemData(Order $order)
    {
        $items = [];

        if (!$order->orderItems) {
            return $items;
        }
        /** @var OrderItem $orderItem */
        foreach ($order->orderItems as $orderItem) {
            $orderItemData = $orderItem->toArray();
            $amount = $orderItemData['amounts'][0];
            $priceGross = $amount->priceGross;
            $tax = $priceGross - $priceGross * 100 / ($orderItem->vatRate + 100.);
            $orderItemData['tax'] = (int) round($tax * 100);

            $orderItemData['price'] = (int) round($priceGross * 100);
            $orderItemData['name'] = $orderItem->orderItemName;

            $items[] = $orderItemData;
        }

        return $items;
    }

    /**
     * @param Address $addressObj
     * @param $customerId
     *
     * @return array
     */
    protected function getCustomerData(Address $addressObj, $customerId)
    {
        $address = $addressObj->toArray();
        if (!$address) {
            return ['customerId' => $customerId];
        }
        $customerData = [
            'email' => (string) $addressObj->email,
            'firstname' => (string) $addressObj->firstName,
            'lastname' => (string) $addressObj->lastName,
            'title' => '', // (string)$addressObj->title: '',
            'birthday' => $this->getBirthDay($addressObj),
            'language' => $addressObj->country->lang,
            'ip' => (string) $this->shopHelper->getIpAddress(),
            'customerId' => (string) $customerId,
            'registrationDate' => '1970-01-01',
            'group' => 'default',
            'company' => (string) $addressObj->companyName,
            'telephonenumber' => (string) $addressObj->phone,
            'language' => $this->shopHelper->getCurrentLanguage(),
        ];
        //TODO: Check format
        $customerData['gender'] = 'm';

        return $customerData;
    }

    /**
     * @param $paymentCode
     * @param $transactionId
     *
     * @return array
     */
    protected function getDefaultRequestData($paymentCode, $transactionId)
    {
        return [
            'paymentCode' => $this->mapPaymentCode($paymentCode),
            'systemInfo' => $this->getSystemInfo(),
            'context' => $this->getApiContextParams($paymentCode, $transactionId),
        ];
    }

    /**
     * @param string $paymentCode
     * @param $transactionId
     *
     * @return array
     */
    protected function getApiContextParams($paymentCode, $transactionId)
    {
        $apiContextParams = [];

        $apiContextParams['aid'] = $this->config->get('aid');
        $apiContextParams['mid'] = $this->config->get('mid');
        $apiContextParams['portalid'] = $this->config->get('portalid');
        $apiContextParams['key'] = $this->config->get('key');
        $mode = $this->config->get('mode');
        $apiContextParams['mode'] = ($mode == 1) ? 'test' : 'live';

        return $apiContextParams;
    }

    /**
     * @param Address $addressObj
     *
     * @return string
     */
    protected function getBirthDay(Address $addressObj): string
    {
        if (!$addressObj->birthday) {
            return '1970-01-01';
        }

        return date('Y-m-d', $addressObj->birthday);
    }

    /**
     * @return array
     */
    protected function getInvoiceData()
    {
        //TODO:
        /** @see Document::$numberWithPrefix curently only accessible via REST api */

        return [
            'invoiceId' => '',
        ];
    }

    /**
     * @param Basket $basket
     *
     * @return array
     */
    protected function getBasketData(Basket $basket)
    {
        $requestParams = $basket->toArray();
        $requestParams['currency'] = (bool) $basket->currency ? $basket->currency : ShopHelper::DEFAULT_CURRENCY;
        $requestParams['grandTotal'] = $basket->basketAmount;
        $requestParams['cartId'] = $basket->id;

        return $requestParams;
    }

    /**
     * @param Order $order
     *
     * @return array
     */
    protected function getBasketDataFromOrder(Order $order)
    {
        $requestParams = $order->toArray();
        $requestParams['grandTotal'] = (int) round($requestParams['amounts'][0]['grossTotal'] * 100);
        $requestParams['cartId'] = $order->id;
        $requestParams['currency'] = $requestParams['amounts'][0]['currency'];

        return $requestParams;
    }

    /**
     * @param $paymentCode
     *
     * @return bool|string
     */
    private function mapPaymentCode($paymentCode)
    {
        return substr($paymentCode, 11);
    }

    /**
     * @return array
     */
    private function getSystemInfo()
    {
        return [
            'vendor' => 'arvatis media GmbH',
            'version' => 7,
            'type' => 'Webshop',
            'url' => $this->shopHelper->getPlentyDomain(),
            'module' => 'plentymarkets 7 Payone plugin',
            'module_version' => 1,
        ];
    }
}
