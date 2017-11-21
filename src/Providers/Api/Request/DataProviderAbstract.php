<?php

namespace Payone\Providers\Api\Request;

use Payone\Adapter\Config as ConfigAdapter;
use Payone\Adapter\SessionStorage;
use Payone\Helpers\AddressHelper;
use Payone\Helpers\ShopHelper;
use Payone\Services\RequestDataValidator;
use Plenty\Modules\Account\Address\Models\Address;
use Plenty\Modules\Basket\Models\Basket;
use Plenty\Modules\Basket\Models\BasketItem;
use Plenty\Modules\Frontend\Session\Storage\Contracts\FrontendSessionStorageFactoryContract;
use Plenty\Modules\Item\Item\Contracts\ItemRepositoryContract;
use Plenty\Modules\Item\Item\Models\Item;
use Plenty\Modules\Item\Item\Models\ItemText;
use Plenty\Modules\Order\Models\Order;
use Plenty\Modules\Order\Models\OrderItem;

/**
 * Class DataProviderAbstract
 */
abstract class DataProviderAbstract
{
    const ACCOUNT_DATA_KEY = 'paymentAccount';
    /**
     * @var FrontendSessionStorageFactoryContract
     */
    protected $sessionStorageFactory;
    /**
     * @var ItemRepositoryContract
     */
    protected $itemRepo;
    /**
     * @var ShopHelper
     */
    protected $shopHelper;
    /**
     * @var AddressHelper
     */
    protected $addressHelper;
    /**
     * @var ConfigAdapter
     */
    protected $config;
    /**
     * @var RequestDataValidator
     */
    protected $validator;
    /**
     * @var SessionStorage
     */
    protected $sessionStorage;

    /**
     * DataProviderAbstract constructor.
     * @param ItemRepositoryContract $itemRepo
     * @param FrontendSessionStorageFactoryContract $sessionStorageFactory
     * @param ShopHelper $shopHelper
     * @param AddressHelper $addressHelper
     * @param ConfigAdapter $config
     * @param RequestDataValidator $validator
     * @param SessionStorage $sessionStorage
     */
    public function __construct(
        ItemRepositoryContract $itemRepo,
        FrontendSessionStorageFactoryContract $sessionStorageFactory,
        ShopHelper $shopHelper,
        AddressHelper $addressHelper,
        ConfigAdapter $config,
        RequestDataValidator $validator,
        SessionStorage $sessionStorage
    ) {
        $this->itemRepo = $itemRepo;
        $this->sessionStorageFactory = $sessionStorageFactory;
        $this->shopHelper = $shopHelper;
        $this->addressHelper = $addressHelper;
        $this->config = $config;
        $this->validator = $validator;
        $this->sessionStorage = $sessionStorage;
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
            $orderItemData['tax'] = (int)round($tax * 100);

            $orderItemData['price'] = (int)round($priceGross * 100);
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
            'email' => (string)$addressObj->email,
            'firstname' => (string)$addressObj->firstName,
            'lastname' => (string)$addressObj->lastName,
            'title' => '', // (string)$addressObj->title: '',
            'birthday' => $this->getBirthDay($addressObj),
            'ip' => (string)$this->shopHelper->getIpAddress(),
            'customerId' => (string)$customerId,
            'registrationDate' => '1970-01-01',
            'group' => 'default',
            'company' => (string)$addressObj->companyName,
            'telephonenumber' => (string)$addressObj->phone,
            'language' => $this->shopHelper->getCurrentLanguage(),
        ];
        //TODO: Check format
        $customerData['gender'] = 'm';

        return $customerData;
    }

    /**
     * @param $paymentCode
     * @return array
     */
    protected function getDefaultRequestData($paymentCode)
    {
        return [
            'paymentMethod' => $this->mapPaymentCode($paymentCode),
            'systemInfo' => $this->getSystemInfo(),
            'context' => $this->getApiContextParams(),
        ];
    }

    /**
     * @return array
     */
    protected function getApiContextParams()
    {
        $apiContextParams = [];

        $apiContextParams['aid'] = $this->config->get('aid');
        $apiContextParams['mid'] = $this->config->get('mid');
        $apiContextParams['portalid'] = $this->config->get('portalid');
        $apiContextParams['key'] = $this->config->get('key');
        $mode = $this->config->get('mode');
        $apiContextParams['mode'] = ($mode == 1) ? 'live' : 'test';

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
        $requestParams['currency'] = (bool)$basket->currency ? $basket->currency : ShopHelper::DEFAULT_CURRENCY;
        $requestParams['grandTotal'] = (int)round($basket->basketAmount * 100);
        $requestParams['itemSumNet'] = (int)round($basket->itemSumNet * 100);
        $requestParams['basketAmount'] = (int)round($basket->basketAmount * 100);
        $requestParams['basketAmountNet'] = (int)round($basket->basketAmountNet * 100);
        $requestParams['shippingAmount'] = (int)round($basket->shippingAmount * 100);
        $requestParams['shippingAmountNet'] = (int)round($basket->shippingAmountNet * 100);

        $uniqueBasketId = $this->getUniqueBasketId($basket->id);

        $requestParams['id'] = $uniqueBasketId;

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
        $requestParams['grandTotal'] = (int)round($requestParams['amounts'][0]['grossTotal'] * 100);
        $requestParams['itemSumNet'] = (int)round($requestParams['amounts'][0]['itemSumNet'] * 100);
        $requestParams['basketAmount'] = (int)round($requestParams['amounts'][0]['basketAmount'] * 100);
        $requestParams['basketAmountNet'] = (int)round($requestParams['amounts'][0]['basketAmountNet'] * 100);
        $requestParams['shippingAmount'] = (int)round($requestParams['amounts'][0]['shippingAmount'] * 100);
        $requestParams['shippingAmountNet'] = (int)round($requestParams['amounts'][0]['shippingAmountNet'] * 100);
        $requestParams['currency'] = $requestParams['amounts'][0]['currency'];

        return $requestParams;
    }

    /**
     * @param $paymentCode
     *
     * @return string
     */
    protected function mapPaymentCode($paymentCode)
    {
        $words = explode('_', str_replace('PAYONE_PAYONE_', '', $paymentCode));

        $paymentCodeLib = '';

        foreach ($words as $word) {
            $paymentCodeLib .= ucfirst(strtolower($word));
        }
        return $paymentCodeLib;
    }

    /**
     * @return array
     */
    protected function getSystemInfo()
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

    /**
     * @param $basketId
     * @return string
     */
    public function getUniqueBasketId($basketId): string
    {
        $maxLengthAll = 12;
        $lengthTime = strlen('' . time());
        $maxLengthTime = $maxLengthAll - strlen($basketId);
        $time = time() . '';
        if ($maxLengthTime < $lengthTime) {
            $time = substr($time, $lengthTime - $maxLengthTime, $lengthTime);
        }
        // workaround for basketid not beeing updated
        $uniqueBasketId = $basketId . '-' . $time;
        return $uniqueBasketId;
    }

    protected function getSequenceNumber($order)
    {
        return 1;//TODO: persist sequencenumber per order
    }

    /**
     * @param $orderId
     *
     * @return array
     */
    protected function getOrderData(Order $order)
    {
        $amount = $order->amounts[0];

        return [
            'orderId' => $order->id,
            'amount' => (int)round($amount->invoiceTotal * 100),
            'currency' => $amount->currency,
        ];
    }
}
