<?php

namespace Payone\Views;

use Payone\Helpers\ShopHelper;
use Payone\Methods\PaymentAbstract;
use Payone\Models\CreditCardCheck;
use Payone\PluginConstants;
use Plenty\Plugin\Templates\Twig;

/**
 * Class PaymentRenderer
 */
class PaymentRenderer
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
     * @var CreditCardCheck
     */
    private $creditCardCheck;

    /**
     * PaymentRenderer constructor.
     *
     * @param Twig $twig
     * @param ShopHelper $shopHelper
     * @param CreditCardCheck $creditCardCheck
     */
    public function __construct(
        Twig $twig,
        ShopHelper $shopHelper,
        CreditCardCheck $creditCardCheck
    ) {
        $this->twig = $twig;
        $this->shopHelper = $shopHelper;
        $this->creditCardCheck = $creditCardCheck;
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
            PluginConstants::NAME . '::Partials.PaymentForm.' . $payment->getCode(),
            [
                'paymentMethod' => $payment,
                'errorMessage' => $message,
                'locale' => $this->shopHelper->getCurrentLocale(),
                'creditcardcheck' => \json_encode($this->creditCardCheck),
            ]
        );
    }
}
