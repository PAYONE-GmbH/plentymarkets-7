<?php


namespace Payone\Providers\DataProviders;


use Payone\Helpers\PaymentHelper;
use Payone\Methods\PayoneAmazonPayPaymentMethod;
use Payone\PluginConstants;
use Plenty\Modules\Basket\Contracts\BasketRepositoryContract;
use Plenty\Plugin\Templates\Twig;

class AmazonPayIntegration
{
    /**
     * @param Twig $twig
     * @param PaymentHelper $paymentHelper
     * @return string
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function call(
        Twig $twig,
        PaymentHelper $paymentHelper)
    {
        $amazonPayMopId = $paymentHelper->getMopId(PayoneAmazonPayPaymentMethod::PAYMENT_CODE);

        return $twig->render(
            PluginConstants::NAME . '::Checkout.AmazonPayCheckout',
            [
                'amazonPayMopId' => $amazonPayMopId
            ]);
    }
}
