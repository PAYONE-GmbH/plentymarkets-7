<?php

namespace Payone\Providers\DataProviders;

use Payone\Helpers\PaymentHelper;
use Payone\Helpers\ShopHelper;
use Payone\Models\CreditCardCheckRequestData;
use Payone\Models\CreditCardStyle;
use Payone\Models\CreditcardTypes;
use Payone\PluginConstants;
use Plenty\Modules\Basket\Contracts\BasketRepositoryContract;
use Plenty\Modules\Webshop\Helpers\UrlQuery;
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
     * @param BasketRepositoryContract $basketRepository
     * @return string
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
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
                'creditcardcheck' => $creditCardCheck,
                'allowedCCTypes' => $creditcardTypes->getAllowedTypes(),
                'defaultWidthInPx' => $style->getDefaultWidthInPx(),
                'defaultHeightInPx' => $style->getDefaultHeightInPx(),
                'defaultStyle' => $style->getDefaultStyle(),
                'payment' => $paymentHelper->getPaymentCodeByMop($selectedPaymentId),
                'trailingSlash' => UrlQuery::shouldAppendTrailingSlash() ? "/" : ""
            ]
        );
    }
}
