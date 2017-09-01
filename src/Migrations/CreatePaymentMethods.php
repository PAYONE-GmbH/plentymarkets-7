<?php

namespace Payone\Migrations;

use Payone\Helpers\PaymentHelper;
use Payone\PluginConstants;
use Plenty\Modules\Payment\Method\Contracts\PaymentMethodRepositoryContract;

/**
 * Migration to create payment mehtods
 *
 * Class CreatePaymentMethod
 */
class CreatePaymentMethods
{
    /**
     * @var PaymentMethodRepositoryContract
     */
    private $paymentMethodRepo;

    /**
     * @var PaymentHelper
     */
    private $paymentHelper;

    /**
     * CreatePaymentMethod constructor.
     *
     * @param PaymentMethodRepositoryContract $paymentMethodRepo
     * @param PaymentHelper $paymentHelper
     */
    public function __construct(
        PaymentMethodRepositoryContract $paymentMethodRepo,
        PaymentHelper $paymentHelper
    ) {
        $this->paymentMethodRepo = $paymentMethodRepo;
        $this->paymentHelper = $paymentHelper;
    }

    /**
     * Run on plugin build
     *
     * Create Payone payment methods
     */
    public function run()
    {
        foreach ($this->paymentHelper->getPaymentCodes() as $paymentCode) {
            if ($this->paymentHelper->getMopId($paymentCode) != 'no_paymentmethod_found') {
                continue;
            }
            $this->paymentMethodRepo->createPaymentMethod(
                [
                    'pluginKey' => PluginConstants::NAME,
                    'paymentKey' => $paymentCode,
                    'name' => $paymentCode,
                ]
            );
        }
    }
}
