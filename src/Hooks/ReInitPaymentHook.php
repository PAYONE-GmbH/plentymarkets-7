<?php

namespace Payone\Hooks;

use Ceres\Helper\LayoutContainer;
use IO\Extensions\Constants\ShopUrls;
use IO\Helper\RouteConfig;
use Plenty\Modules\Payment\Method\Contracts\PaymentMethodRepositoryContract;
use Plenty\Modules\Payment\Method\Models\PaymentMethod;
use Plenty\Plugin\Templates\Twig;
use Payone\PluginConstants;

class ReInitPaymentHook
{
    public function handle(LayoutContainer $layoutContainer, $order)
    {
        /** @var PaymentMethodRepositoryContract $paymentMethodRepository */
        $paymentMethodRepository = pluginApp(PaymentMethodRepositoryContract::class);
        $paymentMethods          = $paymentMethodRepository->allForPlugin(PluginConstants::NAME);
        $paymentIds              = [];
        foreach ($paymentMethods as $paymentMethod) {
            if ($paymentMethod instanceof PaymentMethod) {
                $paymentIds[] = $paymentMethod->id;
            }
        }
        if(in_array($order->methodOfPaymentId, $paymentIds)) {
            $paymentMethodId = $order->methodOfPaymentId;
        }else{
            $paymentMethodId = 0;
        }

        /** @var ShopUrls $shopUrls */
        $shopUrls = pluginApp(ShopUrls::class);
        $isMyAccount = $shopUrls->is(RouteConfig::MY_ACCOUNT);
        $isOrderConfirmation = $shopUrls->is(RouteConfig::CONFIRMATION);

        /** @var Twig $twig */
        $twig = pluginApp(Twig::class);
        $layoutContainer->addContent($twig->render(PluginConstants::NAME . '::Checkout.ReinitPayment', [
            'order' => $order,
            "paymentMethodId" => $paymentMethodId,
            'isMyAccount' => $isMyAccount,
            'isOrderConfirmation' => $isOrderConfirmation
        ])
        );
    }
}
