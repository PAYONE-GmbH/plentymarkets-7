<?php

namespace Payone\Providers\Api\Request;

use Plenty\Modules\Basket\Models\Basket;

class ManagemandateDataProvider extends DataProviderAbstract implements DataProviderBasket
{
    /**
     * @param string $paymentCode
     * @param Basket $basket
     * @param string|null $requestReference
     * @param int|null $clientId
     * @param int|null $pluginSetId
     * @return array
     * @throws \Exception
     */
    public function getDataFromBasket(string $paymentCode, Basket $basket, string $requestReference = null, int $clientId = null, int $pluginSetId = null): array
    {
        $requestParams = $this->getDefaultRequestData($paymentCode, $clientId, $pluginSetId);

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
