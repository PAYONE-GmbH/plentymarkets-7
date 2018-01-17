<?php

namespace Payone\Views;

use Payone\Helpers\ShopHelper;
use Payone\PluginConstants;
use Plenty\Plugin\Templates\Twig;

/**
 * Class CheckoutErrorRenderer
 */
class CheckoutErrorRenderer
{
    /**
     * @var ShopHelper
     * */
    private $shopHelper;
    /**
     * @var Twig
     */
    private $twig;

    /**
     * CheckoutErrorRenderer constructor.
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
     * @param $message
     *
     * @return string
     */
    public function renderErrorMessage($message): string
    {
        if (!$message) {
            return '';
        }

        return $this->twig->render(
            PluginConstants::NAME . '::Partials.Error',
            [
                'errorMessage' => $message,
                'isDebugModeEnabled' => $this->shopHelper->isDebugModeActive(),
            ]
        );
    }
}
