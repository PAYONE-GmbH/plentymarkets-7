<?php

namespace Payone\Migrations;

use Plenty\Modules\Payment\Method\Contracts\PaymentMethodRepositoryContract;
use Payone\Helper\PaymentHelper;

/**
 * Migration to create payment mehtods
 *
 * Class CreatePaymentMethod
 *
 * @package Payone\Migrations
 */
class CreatePaymentMethod
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
     * @param PaymentHelper                   $paymentHelper
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
        if ($this->paymentHelper->getPayoneMopId() != 'no_paymentmethod_found') {
            return;
        }

        foreach ($this->paymentHelper->getPayonePaymentCodes() as $paymentCode) {
            $this->paymentMethodRepo->createPaymentMethod(
                [
                'pluginKey' => 'Payone',
                'paymentKey' => $paymentCode,
                'name' => $paymentCode
                ]
            );
        }
    }
}
