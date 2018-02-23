<?php

namespace Payone\Providers\Api\Request;

use Plenty\Modules\Basket\Models\Basket;

class ManagemandateDataProvider extends DataProviderAbstract implements DataProviderBasket
{
    /**
     * {@inheritdoc}
     */
    public function getDataFromBasket(string $paymentCode, Basket $basket, string $requestReference = null)
    {
        $requestParams = $this->getDefaultRequestData($paymentCode);

        $requestParams['basket'] = $this->getBasketData($basket);

        $requestParams['basketItems'] = $this->getCartItemData($basket);
        $requestParams['shippingAddress'] = $this->addressHelper->getAddressData(
            $this->addressHelper->getBasketShippingAddress($basket)
        );
        $billingAddress = $this->addressHelper->getBasketBillingAddress($basket);
        $requestParams['billingAddress'] = $this->addressHelper->getAddressData(
            $billingAddress
        );
        $requestParams['customer'] = $this->getCustomerData($billingAddress, $basket->customerId);

        $requestParams['bankAccount'] = $this->getBankAccount();
        $this->validator->validate($requestParams);

        return $requestParams;
    }

}
