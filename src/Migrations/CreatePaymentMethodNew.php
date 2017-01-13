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
class CreatePaymentMethodNew
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
        foreach ($this->paymentHelper->getPayonePaymentCodes() as $paymentCode) {
            if ($this->paymentHelper->getPayoneMopId($paymentCode) != 'no_paymentmethod_found') {
                continue;
            }
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
