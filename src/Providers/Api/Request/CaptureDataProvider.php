<?php

namespace Payone\Providers\Api\Request;

use Payone\Adapter\Config as ConfigAdapter;
use Payone\Adapter\SessionStorage;
use Payone\Helpers\AddressHelper;
use Payone\Helpers\PaymentHelper;
use Payone\Helpers\ShopHelper;
use Payone\Services\RequestDataValidator;
use Plenty\Modules\Account\Address\Contracts\AddressRepositoryContract;
use Plenty\Modules\Account\Contact\Contracts\ContactRepositoryContract;
use Plenty\Modules\Account\Contracts\AccountRepositoryContract;
use Plenty\Modules\Frontend\Events\FrontendUpdateInvoiceAddress;
use Plenty\Modules\Frontend\Session\Storage\Contracts\FrontendSessionStorageFactoryContract;
use Plenty\Modules\Item\Item\Contracts\ItemRepositoryContract;
use Plenty\Modules\Order\Models\Order;
use Plenty\Modules\Order\Shipping\Countries\Contracts\CountryRepositoryContract;
use Plenty\Modules\Order\Shipping\Information\Contracts\ShippingInformationRepositoryContract;
use Plenty\Modules\Order\Shipping\ServiceProvider\Contracts\ShippingServiceProviderRepositoryContract;

/**
 * Class CaptureDataProvider
 */
class CaptureDataProvider extends DataProviderAbstract implements DataProviderOrder
{
    /** @var FrontendUpdateInvoiceAddress */
    protected $invoice;

    /**
     * @var ShippingInformationRepositoryContract
     */
    private $shippingInformationRepo;

    /**
     * ApiRequestDataProvider constructor.
     *
     * @param PaymentHelper $paymentHelper
     * @param AddressRepositoryContract $addressRepo
     * @param SessionStorage $sessionStorage
     * @param ItemRepositoryContract $itemRepo
     * @param CountryRepositoryContract $countryRepo
     * @param ShippingServiceProviderRepositoryContract $shippingRepo
     * @param ContactRepositoryContract $contactRepositoryContract
     * @param FrontendSessionStorageFactoryContract $sessionStorageFactory
     * @param AccountRepositoryContract $accountRepositoryContract
     * @param ShopHelper $shopHelper
     * @param AddressHelper $addressHelper
     * @param ConfigAdapter $config
     * @param ShippingInformationRepositoryContract $shippingInformationRepo
     */
    public function __construct(
        PaymentHelper $paymentHelper,
        AddressRepositoryContract $addressRepo,
        SessionStorage $sessionStorage,
        ItemRepositoryContract $itemRepo,
        CountryRepositoryContract $countryRepo,
        ShippingServiceProviderRepositoryContract $shippingRepo,
        ContactRepositoryContract $contactRepositoryContract,
        FrontendSessionStorageFactoryContract $sessionStorageFactory,
        AccountRepositoryContract $accountRepositoryContract,
        ShopHelper $shopHelper,
        AddressHelper $addressHelper,
        ConfigAdapter $config,
        RequestDataValidator $validator,
        ShippingInformationRepositoryContract $shippingInformationRepo
    ) {
        parent::__construct(
            $paymentHelper,
            $addressRepo,
            $sessionStorage,
            $itemRepo,
            $countryRepo,
            $shippingRepo,
            $contactRepositoryContract,
            $sessionStorageFactory,
            $accountRepositoryContract,
            $shopHelper,
            $addressHelper,
            $config,
            $validator
        );
        $this->shippingInformationRepo = $shippingInformationRepo;
    }

    /**
     * @param string $paymentCode
     * @param Order $order
     * @param string|null $requestReference
     */
    public function getDataFromOrder(string $paymentCode, Order $order, string $requestReference = null)
    {
        $requestParams = $this->getDefaultRequestData($paymentCode, 'order-' . $order->id); //TODO: get transaction id

        $requestParams['basket'] = $this->getBasketDataFromOrder($order);
        $requestParams['basketItems'] = $this->getOrderItemData($order);

        $billingAddress = $this->addressHelper->getOrderBillingAddress($order);
        $requestParams['billingAddress'] = $this->addressHelper->getAddressData(
            $billingAddress
        );
        $requestParams['customer'] = $this->getCustomerData($billingAddress, $order->ownerId);

        $requestParams['referenceId'] = $requestReference;

        $requestParams['invoice'] = $this->getInvoiceData();
        $requestParams['order'] = $this->getOrderData($order->id);
        $requestParams['tracking'] = $this->getTrackingData($order->id);

        $this->validator->validate($requestParams);

        return $requestParams;
    }

    /**
     * @param $orderId
     *
     * @return array
     */
    protected function getOrderData($orderId)
    {
        return ['orderId' => $orderId];
    }

    /**
     * @param $orderId
     *
     * @return array
     */
    protected function getTrackingData($orderId)
    {
        try {
            $shippingInfo = $this->shippingInformationRepo->getShippingInformationByOrderId($orderId);
        } catch (\Exception $e) {
            return [];
        }

        return [
            'trackingId' => $shippingInfo->transactionId,
            'returnTrackingId' => '',
            'shippingCompany' => $shippingInfo->shippingServiceProvider,
        ];
    }
}
