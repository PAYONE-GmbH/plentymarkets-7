<?php

namespace Payone\Providers\Api\Request;

use Plenty\Modules\Basket\Models\Basket;

interface DataProviderBasket
{
    /**
     * @param string $paymentCode
     * @param Basket $basket
     * @param string|null $requestReference
     *
     * @return array
     */
    public function getDataFromBasket(string $paymentCode, Basket $basket, string $requestReference = null);
}
