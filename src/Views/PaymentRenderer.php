<?php

namespace Payone\Views;

use Payone\Helpers\ShopHelper;
use Payone\Methods\PaymentAbstract;
use Payone\Models\CreditCardCheck;
use Payone\Models\CreditcardTypes;
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
     * @var CreditcardTypes
     */
    private $creditcardTypes;

    /**
     * PaymentRenderer constructor.
     *
     * @param Twig $twig
     * @param ShopHelper $shopHelper
     * @param CreditCardCheck $creditCardCheck
     * @param CreditcardTypes $creditcardTypes
     */
    public function __construct(
        Twig $twig,
        ShopHelper $shopHelper,
        CreditCardCheck $creditCardCheck,
    CreditcardTypes $creditcardTypes
    ) {
        $this->twig = $twig;
        $this->shopHelper = $shopHelper;
        $this->creditCardCheck = $creditCardCheck;
        $this->creditcardTypes = $creditcardTypes;
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
                'creditcardcheck' => $this->creditCardCheck,
                'ccTypes' => $this->creditcardTypes->getAllowedTypes()
            ]
        );
    }
}
