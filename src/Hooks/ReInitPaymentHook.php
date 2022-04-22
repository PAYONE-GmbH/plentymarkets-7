<?php

namespace Payone\Hooks;

use Ceres\Helper\LayoutContainer;
use IO\Extensions\Constants\ShopUrls;
use IO\Helper\RouteConfig;
use Payone\Adapter\SessionStorage;
use Payone\Helpers\OrderHelper;
use Payone\Helpers\PaymentHelper;
use Payone\Helpers\ShopHelper;
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

        $orderId = $order['id'];
        /** @var OrderHelper $orderHelper */
        $orderHelper = pluginApp(OrderHelper::class);
        $orderNew = $orderHelper->getOrderByOrderId($orderId);

        $mopId = $orderNew->methodOfPaymentId;

        /** @var PaymentMethodRepositoryContract $paymentMethodRepository */
        $paymentMethodRepository = pluginApp(PaymentMethodRepositoryContract::class);
        $paymentMethods = $paymentMethodRepository->allForPlugin(PluginConstants::NAME);
        $paymentIds = [];
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
        if (!empty($paymentRepo->getPaymentsByOrderId($order['id']))) {
            $orderHasPaymentAssigned = 1;
        }


        /** @var SettingsService $settingsService */
        $settingsService = pluginApp(SettingsService::class);

        if ($mopId == $amazonPayMopId) {
            /** @var Twig $twig */
            $twig = pluginApp(Twig::class);

            // set currency $order->amount->currency to session
            /** @var SessionStorage $sessionStorage */
            $sessionStorage = pluginApp(SessionStorage::class);
            $sessionStorage->setSessionValue('currencyFromOrder', $orderNew->amount->currency);
            $sessionStorage->setSessionValue('amountFromOrder', $orderNew->amount->invoiceTotal);
            $layoutContainer->addContent($twig->render(PluginConstants::NAME . '::MyAccount.AmazonPayConfirmation', [
                'selectedPaymentId' => $mopId,
                'amazonPayMopId' => $amazonPayMopId,
                'sandbox' => (bool)$settingsService->getPaymentSettingsValue('Sandbox', PayoneAmazonPayPaymentMethod::PAYMENT_CODE),
                'orderId' => $orderId,
                'orderHasPayment' => $orderHasPaymentAssigned,
                "paymentIds" => $paymentIds,
                'trailingSlash' => ShopHelper::getTrailingSlash()
            ])
            );
        } else {
            /** @var Twig $twig */
            $twig = pluginApp(Twig::class);
            $layoutContainer->addContent($twig->render(PluginConstants::NAME . '::MyAccount.ReinitPayment', [
                'order' => $order,
                "paymentIds" => $paymentIds,
                'isMyAccount' => $isMyAccount,
                'isOrderConfirmation' => $isOrderConfirmation,
                'orderHasPayment' => $orderHasPaymentAssigned,
                'trailingSlash' => ShopHelper::getTrailingSlash()
            ])
            );
        }
    }
}
