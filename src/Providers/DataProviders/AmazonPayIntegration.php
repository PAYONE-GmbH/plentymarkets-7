<?php


namespace Payone\Providers\DataProviders;


use Payone\Helpers\PaymentHelper;
use Payone\Methods\PayoneAmazonPayPaymentMethod;
use Payone\PluginConstants;
use Plenty\Modules\Basket\Contracts\BasketRepositoryContract;
use Plenty\Plugin\Templates\Twig;

class AmazonPayIntegration
{
    public function call(
        Twig $twig,
        BasketRepositoryContract $basketRepository,
        PaymentHelper $paymentHelper)
    {
        $basket = $basketRepository->load();
        $selectedPaymentId = $basket->methodOfPaymentId;
        $amazonPayMopId = $paymentHelper->getMopId(PayoneAmazonPayPaymentMethod::PAYMENT_CODE);


        return $twig->render(
            PluginConstants::NAME . '::Checkout.AmazonPayCheckout',
            [
                'currentPaymentId' => $selectedPaymentId,
                'amazonPayMopId' => $amazonPayMopId
            ]);
    }
}
