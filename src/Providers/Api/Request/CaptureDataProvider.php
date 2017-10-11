<?php

namespace Payone\Providers\Api\Request;

use Plenty\Modules\Order\Models\Order;

/**
 * Class CaptureDataProvider
 */
class CaptureDataProvider extends DataProviderAbstract implements DataProviderOrder
{

    /**
     * {@inheritdoc}
     */
    public function getDataFromOrder(string $paymentCode, Order $order, string $requestReference = null)
    {
        $requestParams = $this->getDefaultRequestData($paymentCode);

        $requestParams['basket'] = $this->getBasketDataFromOrder($order);
        $requestParams['basketItems'] = $this->getOrderItemData($order);

        $billingAddress = $this->addressHelper->getOrderBillingAddress($order);
        $requestParams['billingAddress'] = $this->addressHelper->getAddressData(
            $billingAddress
        );
        $requestParams['customer'] = $this->getCustomerData($billingAddress, $order->ownerId);

        $requestParams['referenceId'] = $requestReference;

        $requestParams['invoice'] = $this->getInvoiceData();
        $requestParams['order'] = $this->getOrderData($order);
        $requestParams['tracking'] = $this->getTrackingData($order->id);
        $requestParams['context']['capturemode'] = $this->getCaptureMode($order);

        $this->validator->validate($requestParams);

        return $requestParams;
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

    /**
     * @param $orderId
     *
     * @return array
     */
    protected function getTrackingData($orderId)
    {
        //TODO:
        return [];
    }

    private function getCaptureMode(Order $order)
    {
        return 'completed';//TODO: do partial captures
    }
}
