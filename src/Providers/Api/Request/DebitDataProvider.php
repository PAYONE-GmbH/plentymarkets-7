<?php

namespace Payone\Providers\Api\Request;

use Plenty\Modules\Order\Models\Order;

/**
 * Class DebitDataProvider
 */
class DebitDataProvider extends DataProviderAbstract implements DataProviderOrder
{
    public function getDataFromOrder(string $paymentCode, Order $order, string $requestReference = null)
    {
        // TODO: Implement getDataFromOrder() method.
        return [];
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
        $requestParams = $this->getDefaultRequestData($paymentCode);
        $requestParams['context']['sequencenumber'] = $this->getSequenceNumber($order);
        $requestParams['basket'] = $this->getBasketDataFromOrder($refund);
        $requestParams['basketItems'] = $this->getOrderItemData($order);
        $requestParams['order'] = $this->getOrderData($refund);
        $requestParams['referenceId'] = $preAuthUniqueId;

        $this->validator->validate($requestParams);

        return $requestParams;
    }
}
