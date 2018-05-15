<?php

namespace Payone\Providers\Api\Request;

use Payone\Methods\PayoneCODPaymentMethod;
use Payone\Methods\PayoneInvoicePaymentMethod;
use Payone\Methods\PayoneInvoiceSecurePaymentMethod;
use Payone\Methods\PayonePrePaymentPaymentMethod;
use Payone\Methods\PayoneSofortPaymentMethod;
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
        $requestParams['context']['capturemode'] = $this->getCaptureMode($paymentCode);
        $requestParams['context']['settleaccount'] = $this->getSettleaccount($paymentCode);
        $requestParams['context']['sequencenumber'] = $this->getSequenceNumber($order);

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
        //TODO:
        return [];
    }

    private function getCaptureMode(string $paymentCode)
    {
        return 'completed'; //TODO: do partial captures
    }

    private function getSettleaccount(string $paymentCode)
    {
        if (
        in_array(
            $paymentCode,
            [
                PayoneInvoiceSecurePaymentMethod::PAYMENT_CODE,
                PayoneInvoicePaymentMethod::PAYMENT_CODE,
                PayonePrePaymentPaymentMethod::PAYMENT_CODE,
                PayoneSofortPaymentMethod::PAYMENT_CODE,
                PayoneCODPaymentMethod::PAYMENT_CODE,

            ]
        )
        ) {
            return 'yes';
        }
        return 'auto';
    }
}
