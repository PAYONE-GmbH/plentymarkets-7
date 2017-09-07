<?php

namespace Payone\Views;

use Payone\Helpers\ShopHelper;
use Payone\Methods\PaymentAbstract;
use Payone\PluginConstants;
use Plenty\Plugin\Templates\Twig;

/**
 * Class PaymentAdditionalData
 */
class PaymentAdditionalData
{
    /**
     * @var Twig
     */
    private $twig;

    /**
     * @var ShopHelper
     */
    private $shopHelper;

    /**
     * PaymentRenderer constructor.
     *
     * @param Twig $twig
     * @param ShopHelper $shopHelper
     */
    public function __construct(
        Twig $twig,
        ShopHelper $shopHelper
    ) {
        $this->twig = $twig;
        $this->shopHelper = $shopHelper;
    }

    /**
     * @param PaymentAbstract $payment
     * @param $message
     *
     * @throws \Exception
     *
     * @return string
     */
    public function render(PaymentAbstract $payment, $message)
    {
        if (!$payment instanceof PaymentAbstract) {
            throw new \Exception('Payment method is not a Payone payment. Can not render it.');
        }

        return $this->twig->render(
            PluginConstants::NAME . '::Partials.PaymentMethod',
            [
                'paymentMethod' => $payment,
                'errorMessage' => $message,
                'locale' => $this->shopHelper->getCurrentLocale(),
            ]
        );
    }
}
