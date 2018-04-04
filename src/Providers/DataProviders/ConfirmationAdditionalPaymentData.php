<?php

namespace Payone\Providers\DataProviders;

use Payone\Adapter\Logger;
use Payone\Helpers\PaymentHelper;
use Payone\Models\Api\AuthResponse;
use Payone\Models\Api\Clearing\Bank;
use Payone\Models\Api\PreAuthResponse;
use Payone\Models\ApiResponseCache;
use Payone\PluginConstants;
use Plenty\Modules\Order\Models\Order;
use Plenty\Modules\Payment\Contracts\PaymentRepositoryContract;
use Plenty\Modules\Payment\Models\Payment;
use Plenty\Plugin\Templates\Twig;

class ConfirmationAdditionalPaymentData
{
    /**
     * @param Twig $twig
     * @param ApiResponseCache $paymentCache
     * @param PaymentHelper $paymentHelper
     * @param Logger $logger
     * @param PaymentRepositoryContract $paymentRepositoryContract
     * @param $arg
     *
     * @return string
     */
    public function call(
        Twig $twig,
        ApiResponseCache $paymentCache,
        PaymentHelper $paymentHelper,
        Logger $logger,
        PaymentRepositoryContract $paymentRepositoryContract,
        $arg
    ) {
        $order = $arg[0];

        $logger->setIdentifier(__METHOD__)->debug('Dataprovider.ConfirmationAdditionalPaymentData', $arg);
        if (!($order instanceof Order)) {
            $logger->setIdentifier(__METHOD__)->debug('Dataprovider.ConfirmationAdditionalPaymentData', 'Not an order.');

            return '';
        }
        $payments = $paymentRepositoryContract->getPaymentsByOrderId($order->id);
        foreach ($payments as $payment) {
            /** @var Payment $payment */
            if (!$paymentHelper->isPayonePayment($payment->mopId)) {
                $logger->setIdentifier(__METHOD__)->debug(
                    'Dataprovider.ConfirmationAdditionalPaymentData',
                    ['Not a Payone payment.', 'payment' => $payment]
                );

                continue;
            }

            /** @var AuthResponse|PreAuthResponse $auth */
            $auth = $paymentCache->loadAuth($payment->mopId);
            $clearing = $auth->getClearing();
            if (!$clearing || !($clearing instanceof Bank)) {
                continue;
            }

            return $twig->render(
                PluginConstants::NAME . '::Partials.ConfirmationAdditionalData',
                [
                    'clearing' => $clearing,
                ]
            );
        }

        return '';
    }
}
