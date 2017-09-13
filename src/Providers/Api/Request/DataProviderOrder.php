<?php

namespace Payone\Providers\Api\Request;

use Plenty\Modules\Order\Models\Order;

interface DataProviderOrder
{
    /**
     * @param string $paymentCode
     * @param Order $order
     * @param string|null $requestReference
     *
     * @return array
     */
    public function getDataFromOrder(string $paymentCode, Order $order, string $requestReference = null);
}
