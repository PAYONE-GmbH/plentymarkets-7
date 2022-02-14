<?php

namespace Payone\Hooks;

use Ceres\Helper\LayoutContainer;
use IO\Extensions\Constants\ShopUrls;
use IO\Helper\RouteConfig;
use Plenty\Modules\Payment\Contracts\PaymentRepositoryContract;
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

        /** @var ShopUrls $shopUrls */
        $shopUrls = pluginApp(ShopUrls::class);
        $isMyAccount = $shopUrls->is(RouteConfig::MY_ACCOUNT);
        $isOrderConfirmation = $shopUrls->is(RouteConfig::CONFIRMATION);

        /** @var PaymentRepositoryContract $paymentRepo */
        $paymentRepo = pluginApp(PaymentRepositoryContract::class);
        $orderHasPaymentAssigned = 0;
        if(!empty($paymentRepo->getPaymentsByOrderId($order['id']))) {
            $orderHasPaymentAssigned = 1;
        }


        /** @var Twig $twig */
        $twig = pluginApp(Twig::class);
        $layoutContainer->addContent($twig->render(PluginConstants::NAME . '::Checkout.ReinitPayment', [
            'order' => $order,
            "paymentIds" => $paymentIds,
            'isMyAccount' => $isMyAccount,
            'isOrderConfirmation' => $isOrderConfirmation,
            'orderHasPayment' => $orderHasPaymentAssigned
         ])
        );
    }
}
