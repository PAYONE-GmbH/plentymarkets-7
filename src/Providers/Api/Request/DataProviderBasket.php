<?php

namespace Payone\Providers\Api\Request;

use Plenty\Modules\Basket\Models\Basket;

interface DataProviderBasket
{
    public function getDataFromBasket(string $paymentCode, Basket $basket, string $requestReference = null);
}
