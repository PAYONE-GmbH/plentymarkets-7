<?php

namespace Payone\Providers\Api\Request;

use Plenty\Modules\Order\Models\Order;

/**
 * Class RefundDataProvider
 */
class RefundDataProvider extends DataProviderAbstract implements DataProviderOrder
{
    /**
     * @param string $paymentCode
     * @param Order $order
     * @param string|null $requestReference
     * @param int|null $clientId
     * @param int|null $pluginSetId
     * @return array
     * @throws \Exception
     */
    public function getDataFromOrder(string $paymentCode, Order $order, string $requestReference = null, int $clientId = null, int $pluginSetId = null): array
    {
        $requestParams = $this->getDefaultRequestData($paymentCode, $clientId, $pluginSetId);
        $requestParams['context']['sequencenumber'] = $this->getSequenceNumber($order);
        $requestParams['basket'] = $this->getBasketDataFromOrder($order);
        $requestParams['order'] = $this->getOrderData($order);

        $requestParams['referenceId'] = $requestReference;

        $this->validator->validate($requestParams);

        return $requestParams;
    }

    /**
     * @param $paymentCode
     * @param Order $order
     * @param Order $refund
     * @param $preAuthUniqueId
     * @param int|null $clientId
     * @param int|null $pluginSetId
     * @return array
     */
    public function getPartialRefundData($paymentCode, Order $order, Order $refund, $preAuthUniqueId, int $clientId = null, int $pluginSetId = null): array
    {
        $requestParams = $this->getDataFromOrder($paymentCode, $order, $preAuthUniqueId, $clientId, $pluginSetId);

        $requestParams['order'] = $this->getOrderData($refund);

        $this->validator->validate($requestParams);

        return $requestParams;
    }
}
