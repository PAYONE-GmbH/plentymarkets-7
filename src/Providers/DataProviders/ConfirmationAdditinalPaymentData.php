<?php

namespace Payone\Providers\DataProviders;

use Payone\Adapter\Logger;
use Payone\Helpers\PaymentHelper;
use Payone\Models\Api\AuthResponse;
use Payone\Models\Api\Clearing\Bank;
use Payone\Models\ApiResponseCache;
use Payone\PluginConstants;
use Plenty\Modules\Order\Models\Order;
use Plenty\Plugin\Templates\Twig;

class ConfirmationAdditinalPaymentData
{
    /**
     * @param Twig $twig
     * @param ApiResponseCache $paymentCache
     * @param PaymentHelper $paymentHelper
     * @param Logger $logger
     * @param $arg
     * @return string
     */
    public function call(
        Twig $twig,
        ApiResponseCache $paymentCache,
        PaymentHelper $paymentHelper,
        Logger $logger,
        $arg
    ) {
        $order = $arg[0];

        $logger->setIdentifier(__METHOD__)->debug('Dataprovider.ConfirmationAdditinalPaymentData', $arg);
        if (!($order instanceof Order)) {
            return '';
        }

        if (!$paymentHelper->isPayonePayment($order->methodOfPaymentId)) {
            return '';
        }

        /** @var AuthResponse $auth */
        $auth = $paymentCache->loadAuth($order->methodOfPaymentId);
        $clearing = $auth->getClearing();
        if (!$clearing || !($clearing instanceof Bank)) {
            return '';
        }

        return $twig->render(
            PluginConstants::NAME . '::Partials.ConfirmationAdditinalPaymentData.twig',
            [
                'clearing' => $clearing,
            ]
        );
    }
}
