<?php

namespace Payone\Providers\DataProviders;

use Payone\Helpers\PaymentHelper;
use Payone\Helpers\ShopHelper;
use Payone\Models\CreditCardCheckRequestData;
use Payone\Models\CreditCardStyle;
use Payone\Models\CreditcardTypes;
use Payone\PluginConstants;
use Plenty\Modules\Basket\Contracts\BasketRepositoryContract;
use Plenty\Plugin\Templates\Twig;

class Javascript
{

    /**
     * @param Twig $twig
     * @param ShopHelper $helper
     * @param CreditCardCheckRequestData $creditCardCheck
     * @param CreditcardTypes $creditcardTypes
     * @param CreditCardStyle $style
     * @param PaymentHelper $paymentHelper
     * @return string
     */
    public function call(
        Twig $twig,
        ShopHelper $helper,
        CreditCardCheckRequestData $creditCardCheck,
        CreditcardTypes $creditcardTypes,
        CreditCardStyle $style,
        PaymentHelper $paymentHelper,
        BasketRepositoryContract $basketRepository
    ) {
        $basket = $basketRepository->load();
        $selectedPaymentId = $basket->methodOfPaymentId;
        return $twig->render(
            PluginConstants::NAME . '::Partials.Javascript',
            [
                'locale' => $helper->getCurrentLanguage(),
                'isDebugModeEnabled' => $helper->isDebugModeActive(),
                'creditcardcheck' => $creditCardCheck,
                'allowedCCTypes' => $creditcardTypes->getAllowedTypes(),
                'defaultWidthInPx' => $style->getDefaultWidthInPx(),
                'defaultHeightInPx' => $style->getDefaultHeightInPx(),
                'defaultStyle' => $style->getDefaultStyle(),
                'payment' => $paymentHelper->getPaymentCodeByMop($selectedPaymentId),
            ]
        );
    }
}