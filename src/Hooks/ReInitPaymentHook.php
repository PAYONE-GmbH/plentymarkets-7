<?php

namespace Payone\Hooks;

use Ceres\Helper\LayoutContainer;
use IO\Extensions\Constants\ShopUrls;
use IO\Helper\RouteConfig;
use Payone\Helpers\PaymentHelper;
use Payone\Services\SettingsService;
use Plenty\Modules\Order\Contracts\OrderRepositoryContract;
use Plenty\Modules\Payment\Contracts\PaymentRepositoryContract;
use Plenty\Modules\Payment\Method\Contracts\PaymentMethodRepositoryContract;
use Plenty\Modules\Payment\Method\Models\PaymentMethod;
use Plenty\Plugin\Templates\Twig;
use Payone\PluginConstants;
use Plenty\Modules\Authorization\Services\AuthHelper;
use Payone\Methods\PayoneAmazonPayPaymentMethod;

class ReInitPaymentHook
{
    public function handle(LayoutContainer $layoutContainer, $order)
    {

        /** @var OrderRepositoryContract $orderContract */
        $orderContract = pluginApp(OrderRepositoryContract::class);

        /** @var \Plenty\Modules\Authorization\Services\AuthHelper $authHelper */
        $authHelper = pluginApp(AuthHelper::class);
        //
        $orderId = $order['id'];
        $orderNew = $authHelper->processUnguarded(
            function () use ($orderContract, $orderId) {
                //unguarded
                return $orderContract->findOrderById($orderId);
            }
        );


        $mopId = $orderNew->methodOfPaymentId;

        /** @var PaymentMethodRepositoryContract $paymentMethodRepository */
        $paymentMethodRepository = pluginApp(PaymentMethodRepositoryContract::class);
        $paymentMethods          = $paymentMethodRepository->allForPlugin(PluginConstants::NAME);
        $paymentIds              = [];
        foreach ($paymentMethods as $paymentMethod) {
            if ($paymentMethod instanceof PaymentMethod) {
                $paymentIds[] = $paymentMethod->id;
            }
        }
        /** @var PaymentHelper $paymentHelper */
        $paymentHelper = pluginApp(PaymentHelper::class);
        $amazonPayMopId = $paymentHelper->getMopId(PayoneAmazonPayPaymentMethod::PAYMENT_CODE);


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


        /** @var SettingsService $settingsService */
        $settingsService = pluginApp(SettingsService::class);

        if($mopId == $amazonPayMopId) {
            /** @var Twig $twig */
            $twig = pluginApp(Twig::class);

            $layoutContainer->addContent($twig->render(PluginConstants::NAME . '::Checkout.AmazonPayConfirmation', [
                'selectedPaymentId' => $mopId,
                'amazonPayMopId' => $amazonPayMopId,
                'sandbox' => (bool)$settingsService->getPaymentSettingsValue('Sandbox', PayoneAmazonPayPaymentMethod::PAYMENT_CODE)
            ])
            );
        } else {

            /** @var Twig $twig */
            $twig = pluginApp(Twig::class);
            $layoutContainer->addContent($twig->render(PluginConstants::NAME . '::Checkout.ReinitPayment', [
                'order' => $order,
                "paymentIds" => $paymentIds,
                'isMyAccount' => $isMyAccount,
                'isOrderConfirmation' => $isOrderConfirmation,
                'orderHasPayment' => $orderHasPaymentAssigned,

            ])
            );
        }
    }
}
