<?php

namespace Payone\Providers\Api\Request;

use Plenty\Modules\Order\Models\Order;

interface DataProviderOrder
{
    public function getDataFromOrder(string $paymentCode, Order $order, string $requestReference = null);
}
