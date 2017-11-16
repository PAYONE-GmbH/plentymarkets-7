<?php

namespace Payone\Methods;

use Payone\Adapter\Logger;
use Payone\Helpers\AddressHelper;
use Plenty\Modules\Basket\Contracts\BasketRepositoryContract;
use Plenty\Modules\Basket\Models\Basket;

class PaymentValidator
{
    /**
     * @var Basket
     */
    private $basket;
    /**
     * @var AddressHelper
     */
    private $addressHelper;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * PaymentValidator constructor.
     *
     * @param BasketRepositoryContract $basket
     * @param AddressHelper $addressHelper
     * @param Logger $logger
     */
    public function __construct(BasketRepositoryContract $basket, AddressHelper $addressHelper, Logger $logger)
    {
        $this->basket = $basket->load();
        $this->addressHelper = $addressHelper;
        $this->logger = $logger;
    }

    /**
     * @param PaymentAbstract $payment
     *
     * @return bool
     */
    public function validate(PaymentAbstract $payment)
    {
        $basketAmount = $this->basket->basketAmount;
        if ($payment->getMinCartAmount() && $basketAmount < $payment->getMinCartAmount()) {
            $this->log($payment->getName(), 'Payment.minCartAmount', $basketAmount);

            return false;
        }

        if ($payment->getMaxCartAmount() && $basketAmount > $payment->getMaxCartAmount()) {
            $this->log($payment->getName(), 'Payment.maxCartAmount', $basketAmount);

            return false;
        }

        $billingAddress = $this->addressHelper->getBasketBillingAddress($this->basket);
        $shippingAddress = $this->addressHelper->getBasketShippingAddress($this->basket);
        if (!$billingAddress) {
            return true;
        }

        $country = $billingAddress->country->isoCode2;
        if (!in_array($country, $payment->getAllowedCountries())) {
            $this->log($payment->getName(), 'Payment.countryNotAllowed', $country);

            return false;
        }

        return true;
    }

    /**
     * @param string $payment
     * @param string $code
     * @param string $value
     */
    protected function log($payment, $code, $value)
    {
        $logger = $this->logger->setIdentifier(__METHOD__);
        $logger->debug(
            $code,
            [
                'basketID' => $this->basket->id,
                'customer' => $this->basket->customerId,
                'payment' => $payment,
                'value' => $value,
            ]
        );
    }
}
