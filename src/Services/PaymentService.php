<?php //strict

namespace Payone\Services;

use Payone\Helper\PaymentHelper;
use Payone\Methods\PayoneCODPaymentMethod;
use Payone\PluginConstants;
use Plenty\Modules\Account\Address\Contracts\AddressRepositoryContract;
use Plenty\Modules\Basket\Models\Basket;
use Plenty\Modules\Basket\Models\BasketItem;
use Plenty\Modules\Item\Item\Contracts\ItemRepositoryContract;
use Plenty\Modules\Item\Item\Models\Item;
use Plenty\Modules\Item\Item\Models\ItemText;
use Plenty\Modules\Order\Shipping\Countries\Contracts\CountryRepositoryContract;
use Plenty\Modules\Order\Shipping\Information\Contracts\ShippingInformationRepositoryContract;
use Plenty\Modules\Payment\Contracts\PaymentRepositoryContract;
use Plenty\Modules\Payment\Method\Contracts\PaymentMethodRepositoryContract;
use Plenty\Modules\Plugin\Libs\Contracts\LibraryCallContract;
use Plenty\Plugin\ConfigRepository;

/**
 * Class PaymentService
 */
class PaymentService
{
    /**
     * @var string
     */
    private $returnType = '';

    /**
     * @var PaymentMethodRepositoryContract
     */
    private $paymentMethodRepository;

    /**
     * @var PaymentRepositoryContract
     */
    private $paymentRepository;

    /**
     * @var PaymentHelper
     */
    private $paymentHelper;

    /**
     * @var LibraryCallContract
     */
    private $libCall;

    /**
     * @var AddressRepositoryContract
     */
    private $addressRepo;

    /**
     * @var ConfigRepository
     */
    private $config;

    /**
     * @var SessionStorageService
     */
    private $sessionStorage;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * PaymentService constructor.
     *
     * @param PaymentMethodRepositoryContract $paymentMethodRepository
     * @param PaymentRepositoryContract $paymentRepository
     * @param ConfigRepository $config
     * @param PaymentHelper $paymentHelper
     * @param LibraryCallContract $libCall
     * @param AddressRepositoryContract $addressRepo
     * @param SessionStorageService $sessionStorage
     * @param Logger $logger
     */
    public function __construct(
        PaymentMethodRepositoryContract $paymentMethodRepository,
        PaymentRepositoryContract $paymentRepository,
        ConfigRepository $config,
        PaymentHelper $paymentHelper,
        LibraryCallContract $libCall,
        AddressRepositoryContract $addressRepo,
        SessionStorageService $sessionStorage,
        Logger $logger
    ) {
        $this->paymentMethodRepository = $paymentMethodRepository;
        $this->paymentRepository = $paymentRepository;
        $this->paymentHelper = $paymentHelper;
        $this->libCall = $libCall;
        $this->addressRepo = $addressRepo;
        $this->config = $config;
        $this->sessionStorage = $sessionStorage;
        $this->returnType = 'continue';
        $this->logger = $logger;
    }

    /**
     * Get the type of payment from the content of the PayPal container
     *
     * @return string
     */
    public function getReturnType()
    {
        /*  'redirectUrl'|'externalContentUrl'|'errorCode'|'continue'  */
        return 'htmlContent';
    }

    /**
     * Get the PayPal payment content
     *
     * @param Basket $basket
     * @param string $mode
     * @return string
     */
    public function getPaymentContent(Basket $basket, $mode = ''): string
    {

        $this->logger->log('getPaymentContent');
        return 'html content';
    }

    /**
     * @return array|string
     */
    public function executePayment($orderId)
    {
        $executeResponse = [];
        $this->returnType = 'errorCode';
        try {
            // Execute the PayPal payment
            $authType = $this->config->get(PluginConstants::NAME . '.authType');
            $this->logger->log('executePayment');
            if ($authType == '1') {
                $requestData = $this->getPreAuthData(null, $orderId);
                $this->logger->log('requestData');
                $this->logger->log($requestData);
                $executeResponse = $this->libCall->call(PluginConstants::NAME . '::auth', $requestData);
                $this->logger->log('responseData');
                $this->logger->log($executeResponse);
            } else {
                $requestData = $this->getPreAuthData(null, $orderId);
                $executeResponse = $this->libCall->call(PluginConstants::NAME . '::preAuth', $requestData);
            }
            if (!isset($executeResponse['success'])) {
                return isset($executeResponse['errorMessage']) ? $executeResponse['errorMessage'] : '';
            }
            $this->returnType = 'success';
        } catch (\Exception $e) {
            $this->logger->log($e->getMessage());
        }

        return $executeResponse;
    }


    /**
     * Fill and return the Paypal parameters
     *
     * @param Basket $basket
     * @param $orderId
     * @return array
     */
    private function getPreAuthData(Basket $basket = null, $orderId)
    {
        $requestParams = [];
        $paymentCode = PayoneCODPaymentMethod::PAYMENT_CODE;
        $requestParams['context'] = $this->paymentHelper->getApiContextParams($paymentCode);

        /** @var Basket $basket */
        $requestParams['basket'] = $basket;

        $requestParams['basketItems'] = $this->getCartItemData($basket);
        $requestParams['shippingAddress'] = $this->getShippingData();
        $requestParams['shippingProvider'] = $this->getShippingProviderData($orderId);
        $requestParams['country'] = $this->getCountryData($basket);

        return $requestParams;
    }

    /**
     * @return array
     */
    private function getShippingData()
    {
        $data = [];
        $shippingAddressId = $this->getShippingAddressId();

        if ($shippingAddressId === false) {
            return $data;
        }

        $shippingAddress = $this->addressRepo->findAddressById($shippingAddressId);
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
     * @return array
     */
    private function getCartItemData(Basket $basket)
    {
        /** @var ItemRepositoryContract $itemContract */
        $itemContract = pluginApp(ItemRepositoryContract::class);
        $items = [];
        /** @var BasketItem $basketItem */
        foreach ($basket->basketItems as $basketItem) {
            /** @var Item $item */
            $item = $itemContract->show($basketItem->itemId);

            $basketItem = $basketItem->getAttributes();

            /** @var ItemText $itemText */
            $itemText = $item->texts;

            $basketItem['name'] = $itemText->first()->name1;

            $items[] = $basketItem;
        }
        return $items;
    }

    /**
     * @param Basket $basket
     * @return array
     */
    private function getCountryData(Basket $basket)
    {
        /** @var CountryRepositoryContract $countryRepo */
        $countryRepo = pluginApp(CountryRepositoryContract::class);

        // Fill the country for PayPal parameters
        $country['isoCode2'] = $countryRepo->findIsoCode($basket->shippingCountryId, 'iso_code_2');
        return $country;
    }

    /**
     * @return bool|mixed
     */
    private function getShippingAddressId()
    {
        $shippingAddressId = $this->sessionStorage->getSessionValue(SessionStorageService::DELIVERY_ADDRESS_ID);

        if ($shippingAddressId == -99) {
            $shippingAddressId = $this->sessionStorage->getSessionValue(SessionStorageService::BILLING_ADDRESS_ID);
        }
        return $shippingAddressId ? $shippingAddressId : false;
    }

    /**
     * @param int $orderId
     * @return array
     */
    private function getShippingProviderData(int $orderId)
    {
        /** @var ShippingInformationRepositoryContract $shippingRepo */
        $shippingRepo = pluginApp(ShippingInformationRepositoryContract::class);
        $shippingInfo = $shippingRepo->getShippingInformationByOrderId($orderId);
        return $shippingInfo->toArray();
    }
}