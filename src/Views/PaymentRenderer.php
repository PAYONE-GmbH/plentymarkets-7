<?php

namespace Payone\Views;

use Payone\Helpers\ShopHelper;
use Payone\Methods\PaymentAbstract;
use Payone\Models\CreditCardCheckRequestData;
use Payone\Models\CreditCardStyle;
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
     * @var CreditCardCheckRequestData
     */
    private $creditCardCheck;
    /**
     * @var CreditcardTypes
     */
    private $creditcardTypes;
    /**
     * @var CreditCardStyle
     */
    private $style;

    /**
     * PaymentRenderer constructor.
     *
     * @param Twig $twig
     * @param ShopHelper $shopHelper
     * @param CreditCardCheckRequestData $creditCardCheck
     * @param CreditcardTypes $creditcardTypes
     * @param CreditCardStyle $style
     */
    public function __construct(
        Twig $twig,
        ShopHelper $shopHelper,
        CreditCardCheckRequestData $creditCardCheck,
        CreditcardTypes $creditcardTypes,
        CreditCardStyle $style
    ) {
        $this->twig = $twig;
        $this->shopHelper = $shopHelper;
        $this->creditCardCheck = $creditCardCheck;
        $this->creditcardTypes = $creditcardTypes;
        $this->style = $style;
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
                'ccTypes' => $this->creditcardTypes->getAllowedTypes(),
                'defaultWidthInPx' => $this->style->getDefaultWidthInPx(),
                'defaultHeightInPx' => $this->style->getDefaultHeightInPx(),
                'defaultStyle' => $this->style->getDefaultStyle(),
                'isDebugModeEnabled' => $this->shopHelper->isDebugModeActive(),
            ]
        );
    }
}
