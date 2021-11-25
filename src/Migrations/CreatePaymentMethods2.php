<?php

namespace Payone\Migrations;

use Payone\Helpers\PaymentHelper;
use Payone\PluginConstants;
use Plenty\Modules\Payment\Method\Contracts\PaymentMethodRepositoryContract;

/**
 * Migration to create payment mehtods
 *
 * Class CreatePaymentMethod2
 */
class CreatePaymentMethods2
{
    /**
     * @var PaymentMethodRepositoryContract
     */
    protected $paymentMethodRepo;

    /**
     * @var PaymentHelper
     */
    protected $paymentHelper;

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

    public function run()
    {
        foreach ($this->paymentHelper->getPaymentCodes() as $paymentCode) {
            // Check whether the ID of the PayPal payment method has been created
            if ($this->paymentHelper->getMopId($paymentCode) == 'no_paymentmethod_found') {
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
}
