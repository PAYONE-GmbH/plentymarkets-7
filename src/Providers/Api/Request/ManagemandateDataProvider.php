<?php

namespace Payone\Providers\Api\Request;

use Payone\Models\BankAccountCache;
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

        if ($this->paymentHasAccount($paymentCode)) {
            $requestParams['account'] = $this->getAccountData();
        }
        $requestParams['bankAccount'] = $this->getBankAccount();
        $this->validator->validate($requestParams);

        return $requestParams;
    }

    private function getBankAccount()
    {
        /** @var BankAccountCache $repo */
        $repo = pluginApp(BankAccountCache::class);

        $account = $repo->loadBankAccount();

        return [
            'holder' => $account->getHolder(),
            'country' => $account->getCountryCode(),
            'bic' => $account->getBic(),
            'iban' => $account->getIban(),
        ];
    }
}
