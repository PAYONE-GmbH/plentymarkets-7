<?php

namespace Payone\Providers\Api\Request;

use Payone\Adapter\SessionStorage;
use Payone\Helpers\AddressHelper;
use Payone\Helpers\ShopHelper;
use Payone\Methods\PayoneAmazonPayPaymentMethod;
use Payone\Methods\PayoneCCPaymentMethod;
use Payone\Methods\PayonePaydirektPaymentMethod;
use Payone\Methods\PayonePayPalPaymentMethod;
use Payone\Methods\PayoneSofortPaymentMethod;
use Payone\Models\BankAccount;
use Payone\Models\BankAccountCache;
use Payone\Models\CreditCardCheckResponseRepository;
use Payone\Models\PaymentConfig\ApiCredentials;
use Payone\Models\SepaMandate;
use Payone\Models\SepaMandateCache;
use Payone\PluginConstants;
use Payone\Services\RequestDataValidator;
use Plenty\Modules\Account\Address\Models\Address;
use Plenty\Modules\Basket\Models\Basket;
use Plenty\Modules\Basket\Models\BasketItem;
use Plenty\Modules\Frontend\Session\Storage\Contracts\FrontendSessionStorageFactoryContract;
use Plenty\Modules\Item\Item\Contracts\ItemRepositoryContract;
use Plenty\Modules\Item\Item\Models\Item;
use Plenty\Modules\Item\Item\Models\ItemText;
use Plenty\Modules\Item\Variation\Contracts\VariationRepositoryContract;
use Plenty\Modules\Item\Variation\Models\Variation;
use Plenty\Modules\Order\Models\Order;
use Plenty\Modules\Order\Models\OrderItem;
use Plenty\Modules\Order\Models\OrderItemType;
use Plenty\Modules\Order\Shipping\Contracts\ParcelServicePresetRepositoryContract;
use Plenty\Modules\Order\Shipping\ParcelService\Models\ParcelServicePreset;
use Plenty\Modules\Payment\Contracts\PaymentRepositoryContract;
use Plenty\Modules\Payment\Models\Payment;
use Plenty\Modules\Payment\Models\PaymentProperty;
use Plenty\Modules\Webshop\Contracts\LocalizationRepositoryContract;
use Plenty\Modules\Webshop\Contracts\WebstoreConfigurationRepositoryContract;
use Plenty\Modules\Item\Property\Models\Property;

/**
 * Class DataProviderAbstract
 */
abstract class DataProviderAbstract
{

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
     * @var ApiCredentials
     */
    protected $config;
    /**
     * @var RequestDataValidator
     */
    protected $validator;
    /**
     * @var ParcelServicePresetRepositoryContract
     */
    private $parcelServicePresetRepository;

    private $paymentRepository;
    /**
     * @var CreditCardCheckResponseRepository
     */
    private $creditCardCheckResponseRepository;
    /**
     * @var SepaMandateCache
     */
    private $sepaMandateCache;

    /**
     * DataProviderAbstract constructor.
     *
     * @param ItemRepositoryContract $itemRepo
     * @param FrontendSessionStorageFactoryContract $sessionStorageFactory
     * @param ShopHelper $shopHelper
     * @param AddressHelper $addressHelper
     * @param ApiCredentials $config
     * @param RequestDataValidator $validator
     * @param ParcelServicePresetRepositoryContract $parcelServicePresetRepository
     * @param PaymentRepositoryContract $paymentRepository
     * @param CreditCardCheckResponseRepository $creditCardCheckResponseRepository
     * @param SepaMandateCache $sepaMandateCache
     */
    public function __construct(
        ItemRepositoryContract $itemRepo,
        FrontendSessionStorageFactoryContract $sessionStorageFactory,
        ShopHelper $shopHelper,
        AddressHelper $addressHelper,
        ApiCredentials $config,
        RequestDataValidator $validator,
        ParcelServicePresetRepositoryContract $parcelServicePresetRepository,
        PaymentRepositoryContract $paymentRepository,
        CreditCardCheckResponseRepository $creditCardCheckResponseRepository,
        SepaMandateCache $sepaMandateCache
    ) {
        $this->itemRepo = $itemRepo;
        $this->shopHelper = $shopHelper;
        $this->addressHelper = $addressHelper;
        $this->config = $config;
        $this->validator = $validator;
        $this->parcelServicePresetRepository = $parcelServicePresetRepository;
        $this->paymentRepository = $paymentRepository;
        $this->creditCardCheckResponseRepository = $creditCardCheckResponseRepository;
        $this->sepaMandateCache = $sepaMandateCache;
    }

    /**
     * @param $basketId
     *
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
    protected function getSepaMandateData()
    {
        /** @var SepaMandate $mandate */
        $mandate = $this->sepaMandateCache->load();

        if (!($mandate instanceof SepaMandate)) {
            return [];
        }

        return $mandate->jsonSerialize() + ['dateofsignature' => date('Ymd')];
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

        /** @var VariationRepositoryContract $variationContract */
        $variationContract = pluginApp(VariationRepositoryContract::class);
        /** @var WebstoreConfigurationRepositoryContract $webstoreConfigurationRepository */
        $webstoreConfigurationRepository = pluginApp(WebstoreConfigurationRepositoryContract::class);
        $webstoreConfiguration = $webstoreConfigurationRepository->getWebstoreConfiguration();

        /** @var LocalizationRepositoryContract $localizationRepository */
        $localizationRepository = pluginApp(LocalizationRepositoryContract::class);
        $language         = $localizationRepository->getLanguage();

        /** @var BasketItem $basketItem */
        foreach ($basket->basketItems as $basketItem) {

            if($basketItem->itemType != BasketItem::BASKET_ITEM_TYPE_VARIATION_ORDER_PROPERTY) {
                /** @var Item $item */
                $item = $this->itemRepo->show($basketItem->itemId);
                /** @var ItemText $itemText */
                $itemText = $item->texts;

                $basketItemArr = $basketItem->toArray();
                $basketItemArr['name'] = $itemText->first()->name1;
                $basketItemArr['price'] = (int)round($basketItem->price * 100);
                $basketItemArr['vat'] = (int)$basketItem->vat;

                $items[] = $basketItemArr;
            }

            if($webstoreConfiguration->useVariationOrderProperties) {
                foreach ($basketItem->basketItemVariationProperties as $basketItemVariationProperty) {
                    if($basketItemVariationProperty instanceof BasketItem) {
                        $basketItemArr = [];
                        $reference = $basketItem->variationId;
                        $name = $itemText->first()->name1;
                        foreach ($basketItemVariationProperty->basketItemOrderParams as $basketItemOrderParam) {
                            $reference .= '_'.$basketItemOrderParam->basketItemId;
                            $name .= ' '.$basketItemOrderParam->value;
                        }

                        $basketItemArr['name'] = $name;
                        $basketItemArr['price'] = (int)round($basketItemVariationProperty->price * 100);
                        $basketItemArr['vat'] = (int)$basketItemVariationProperty->vat;
                        $items[] = $basketItemArr;
                    }
                }
            } else {
                /**
                 * Item property handling for surcharges
                 * For example "Pfand"
                 */
                /** @var Variation $variation */
                $variation = $variationContract->findById($basketItem->variationId);

                $itemProperties = $variation->item->itemProperties;
                if ($itemProperties && count($itemProperties) > 0) {
                    foreach ($itemProperties as $itemProperty) {
                        $basketItemArr = [];
                        $basketItemArr['name'] = $basketItem->variationId . '_' . $itemProperty->id;
                        $basketItemArr['price'] = (int)round($basketItemVariationProperty->price * 100);
                        $basketItemArr['vat'] = (int)$basketItemVariationProperty->vat;

                        $price = $itemProperty->surcharge;
                        $property = $itemProperty->property;
                        if ($property instanceof Property) {
                            if(!$property->isOderProperty && $property->isShownAsAdditionalCosts) {
                                $name = $property->names->first()->name;
                                foreach ($property->names as $propertyName) {
                                    if ($propertyName->lang == $language) {
                                        $name = $propertyName->name;
                                    }
                                }
                                $basketItemArr['name'] = $name;

                                $markup = $property->surcharge;
                                if($property->propertyGroupId > 0 && $property->group->first()->isSurchargePercental) {
                                    $markup = $price / 100 * $property->surcharge;
                                }
                                $price += $markup;

                                $basketItemArr['price'] = (int)round($price * 100);
                                $basketItemArr['vat'] = (int)$basketItem->vat;
                            }
                        }
                        $items[] = $basketItemArr;
                    }
                }
            }
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
            if ($orderItem->typeId == OrderItemType::TYPE_SHIPPING_COSTS) {
                continue;
            }
            $orderItemData = $orderItem->toArray();
            $amount = $orderItemData['amounts'][0];
            $orderItemData['vat'] = (int)$orderItemData['vatRate'];
            $orderItemData['price'] = (int)round($amount['priceGross'] * 100);
            $orderItemData['name'] = $orderItemData['orderItemName'];
            $orderItemData['itemId'] = $orderItemData['id'];

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
            'ip' => (string)$this->shopHelper->getIpAddress(),
            'customerId' => (string)$customerId,
            //'registrationDate' => '1970-01-01', // what the ... is this?
            'group' => 'default',
            'company' => (string)$addressObj->companyName,
            'telephonenumber' => (string)$addressObj->phone,
            'language' => $this->shopHelper->getCurrentLanguage(),
        ];

        $dateOfBirth = $this->getBirthDay($addressObj);
        $customerData['birthday'] = '';
        if(isset($dateOfBirth)) {
            $customerData['birthday'] = $dateOfBirth;
        }

        $customerData['gender'] = 'm';
        if($addressObj->gender == 'female') {
            $customerData['gender'] = 'f';
        }

        $taxIdNumber = $addressObj->taxIdNumber;
        if (!empty($taxIdNumber)) {
            $customerData['businessrelation'] = 'b2b';
        } else {
            $customerData['businessrelation'] = 'b2c';
        }

        return $customerData;
    }

    /**
     * @param string $paymentCode
     * @param int|null $clientId
     * @param int|null $pluginSetId
     * @return array
     */
    protected function getDefaultRequestData(string $paymentCode, int $clientId = null, int $pluginSetId = null): array
    {
        return [
            'paymentMethod' => $this->mapPaymentCode($paymentCode),
            'systemInfo' => $this->getSystemInfo(),
            'context' => $this->getApiContextParams($paymentCode, $clientId, $pluginSetId),
        ];
    }

    /**
     * @param string $paymentCode
     * @param int|null $clientId
     * @param int|null $pluginSetId
     * @return array
     */
    protected function getApiContextParams(string $paymentCode, int $clientId = null, int $pluginSetId = null)
    {
        return $this->config->getApiCredentials($paymentCode, $clientId, $pluginSetId);
    }

    /**
     * @param Address $addressObj
     * @return false|string|null
     */
    protected function getBirthDay(Address $addressObj)
    {
        if(!isset($addressObj->birthday) || !strlen($addressObj->birthday)) {
            return null;
        }
        $dateOfBirth = strtotime($addressObj->birthday);
        return date('Y-m-d', $dateOfBirth);
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
        /** @var \Plenty\Modules\Frontend\Services\VatService $vatService */
        $vatService = pluginApp(\Plenty\Modules\Frontend\Services\VatService::class);

        //we have to manipulate the basket because its stupid and doesnt know if its netto or gross
        if(!count($vatService->getCurrentTotalVats())) {
            $basket->itemSum = $basket->itemSumNet;
            $basket->shippingAmount = $basket->shippingAmountNet;
            $basket->basketAmount = $basket->basketAmountNet;
        }

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
        $requestParams['basketAmount'] = (int)round($requestParams['amounts'][0]['grossTotal'] * 100);
        $requestParams['basketAmountNet'] = (int)round($requestParams['amounts'][0]['netTotal'] * 100);
        $requestParams['shippingAmount'] = (int)round($this->getShippingAmountFromOrder($order) * 100);
        $requestParams['shippingAmountNet'] = (int)round($this->getShippingAmountNetFromOrder($order) * 100);
        $requestParams['currency'] = $requestParams['amounts'][0]['currency'];

        if($order->amount->isNet){
            $requestParams['basketAmount'] = $requestParams['basketAmountNet'];
            $requestParams['shippingAmount'] = $requestParams['shippingAmountNet'];
        }

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
            'vendor' => 'plentysystems AG',
            'version' => 7,
            'type' => 'Webshop',
            'url' => $this->shopHelper->getPlentyDomain(),
            'module' => 'plentymarkets 7 Payone plugin',
            'module_version' => PluginConstants::VERSION,
        ];
    }

    /**
     * @param Order $order
     *
     * @return int
     */
    protected function getSequenceNumber(Order $order)
    {
        $payments = $this->paymentRepository->getPaymentsByOrderId($order->id);

        /* @var $payment Payment */
        foreach ($payments as $payment) {
            /* @var $property PaymentProperty */
            foreach ($payment->properties as $property) {
                if (!($property instanceof PaymentProperty)) {
                    continue;
                }
                if ($property->typeId === PaymentProperty::TYPE_TRANSACTION_CODE) {
                    return 1 + (int)$property->value;
                }
            }
        }

        return 1;
    }

    /**
     * @param Order $order
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

    /**
     * @param $shippingProviderId
     *
     * @return array
     */
    protected function getShippingProvider($shippingProviderId)
    {
        /** @var ParcelServicePreset $preset */
        $preset = $this->parcelServicePresetRepository->getPresetById($shippingProviderId);

        return ['name' => $preset->parcelServiceNames[0]->name];
    }

    /**
     * @param $paymentCode
     *
     * @return bool
     */
    protected function paymentHasRedirect($paymentCode)
    {
        // URLs might be necessary since some cards require REDIRECT for 3d secure
        if (
        in_array(
            $paymentCode,
            [
                PayoneCCPaymentMethod::PAYMENT_CODE,
                PayonePayPalPaymentMethod::PAYMENT_CODE,
                PayonePaydirektPaymentMethod::PAYMENT_CODE,
                PayoneSofortPaymentMethod::PAYMENT_CODE,
                PayoneAmazonPayPaymentMethod::PAYMENT_CODE,
            ]
        )
        ) {
            return true;
        }

        return false;
    }

    /**
     * @return array
     */
    protected function getRedirectUrls($transactionBasketId = "")
    {
        $successParam = '';
        if(strlen($transactionBasketId)){
            $successParam = '?transactionBasketId='.$transactionBasketId;
        }
        return [
            'success' => $this->shopHelper->getPlentyDomain() . '/payment/payone/checkoutSuccess'.$successParam,
            'error' => $this->shopHelper->getPlentyDomain() . '/payment/payone/error',
            'back' => $this->shopHelper->getPlentyDomain() . '/checkout',
        ];
    }

    /**
     * @throws \Exception
     *
     * @return \Payone\Models\CreditCardCheckResponse
     */
    protected function getCreditCardData()
    {
        $check = $this->creditCardCheckResponseRepository->loadLastResponse();

        if (!is_object($check)) {
            throw new \Exception('No valid precheck data found.');
        }

        return $check;
    }

    protected function getBankAccount()
    {
        /** @var BankAccountCache $repo */
        $repo = pluginApp(BankAccountCache::class);

        /** @var BankAccount $account */
        $account = $repo->loadBankAccount();

        if (!($account instanceof BankAccount)) {
            $account = pluginApp(BankAccount::class);
        }

        return $account->jsonSerialize();
    }

    /**
     * @param string $basketId
     * @param $basketAmount
     * @param string $currency
     * @return array
     */
    protected function getAmazonPayData(string $basketId, $basketAmount, string $currency): array
    {
        /** @var SessionStorage $sessionStorage */
        $sessionStorage = pluginApp(SessionStorage::class);
        $amazonAuthConfig = [];
        $amazonAuthConfig['workOrderId'] = $sessionStorage->getSessionValue('workOrderId');
        $amazonAuthConfig['amazonReferenceId'] = $sessionStorage->getSessionValue('amazonReferenceId');
        $amazonAuthConfig['reference'] = $basketId;

        $amazonAuthConfig['currency'] = $currency;
        // amount in smallest unit
        $amazonAuthConfig['amount'] = $basketAmount * 100;

        return $amazonAuthConfig;
    }

    /**
     * @param Order $order
     * @return float
     */
    private function getShippingAmountFromOrder(Order $order)
    {
        /** @var OrderItem $orderItem */
        foreach ($order->orderItems as $orderItem) {
            if ($orderItem->typeId != OrderItemType::TYPE_SHIPPING_COSTS) {
                continue;
            }
            $orderItemData = $orderItem->toArray();
            $amount = $orderItemData['amounts'][0];

            return $amount['priceGross'];
        }

        return 0.;
    }

    /**
     * @param Order $order
     * @return float
     */
    private function getShippingAmountNetFromOrder(Order $order)
    {
        /** @var OrderItem $orderItem */
        foreach ($order->orderItems as $orderItem) {
            if ($orderItem->typeId != OrderItemType::TYPE_SHIPPING_COSTS) {
                continue;
            }
            $orderItemData = $orderItem->toArray();
            $amount = $orderItemData['amounts'][0];

            return $amount['priceNet'];
        }

        return 0;
    }
}
