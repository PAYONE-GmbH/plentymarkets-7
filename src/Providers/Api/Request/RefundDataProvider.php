<?php

namespace Payone\Providers\Api\Request;

use Plenty\Modules\Frontend\Events\FrontendUpdateInvoiceAddress;
use Plenty\Modules\Order\Models\Order;

/**
 * Class RefundDataProvider
 */
class RefundDataProvider extends DataProviderAbstract implements DataProviderOrder
{
    /** @var FrontendUpdateInvoiceAddress */
    protected $invoice;

     /**
     * {@inheritdoc}
     */
    public function getDataFromOrder(string $paymentCode, Order $order, string $requestReference = null)
    {
        $requestParams = $this->getDefaultRequestData($paymentCode, 'order-' . $order->id); //TODO: get transaction id

        $requestParams['basket'] = $this->getBasketDataFromOrder($order);

        $requestParams['basketItems'] = $this->getOrderItemData($order);
        $billingAddress = $this->addressHelper->getOrderBillingAddress($order);
        $requestParams['customer'] = $this->getCustomerData($billingAddress, $order->ownerId);
        $requestParams['referenceId'] = $requestReference;

        $requestParams['invoice'] = $this->getInvoiceData();

        $this->validator->validate($requestParams);

        return $requestParams;
    }

    /**
     * @param $paymentCode
     * @param Order $order
     * @param Order $refund
     * @param $preAuthUniqueId
     *
     * @return array
     */
    public function getPartialRefundData($paymentCode, Order $order, Order $refund, $preAuthUniqueId)
    {
        $requestParams = $this->getDataFromOrder($paymentCode, $order, $preAuthUniqueId);

        $requestParams['basket'] = $this->getBasketDataFromOrder($refund);
        $requestParams['basketItems'] = $this->getOrderItemData($refund);
        $requestParams['context']['transactionId'] = 'order-' . $order->id;

        $this->validator->validate($requestParams);

        return $requestParams;
    }

    /**
     * @param $orderId
     *
     * @return array
     */
    protected function getTrackingData($orderId)
    {
        try {//TODO:
            $shippingInfo = $this->shippingProviderRepository->getShippingInformationByOrderId($orderId);
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
