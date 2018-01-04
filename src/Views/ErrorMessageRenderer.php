<?php

namespace Payone\Views;

use Payone\Helpers\ShopHelper;
use Payone\Methods\PaymentAbstract;
use Payone\PluginConstants;
use Plenty\Plugin\Templates\Twig;

class ErrorMessageRenderer
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
    public function render($errorMessage)
    {
        if (!$errorMessage) {
            return '';
        }

        return $this->twig->render(
            PluginConstants::NAME . '::Partials.ErrorText',
            [
                'errorMessage' => $errorMessage,
                'isDebugModeEnabled' => $this->shopHelper->isDebugModeActive(),
            ]
        );
    }
}
