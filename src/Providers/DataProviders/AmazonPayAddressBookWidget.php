<?php

namespace Payone\Providers\DataProviders;

use Payone\PluginConstants;
use Plenty\Plugin\Templates\Twig;

class AmazonPayAddressBookWidget
{
    /**
     * @param Twig $twig
     * @return string
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function call(Twig $twig)
    {
        return $twig->render(PluginConstants::NAME . '::Checkout.AmazonPayAddressBookWidget');
    }
}
