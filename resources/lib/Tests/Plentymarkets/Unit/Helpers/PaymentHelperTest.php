<?php

namespace Payone\Tests\Unit\Helpers;

use Payone\Helpers\PaymentHelper;
use Payone\Methods\PayoneInvoicePaymentMethod;
use Payone\Methods\PayonePaydirektPaymentMethod;
use Payone\Methods\PayonePayolutionInstallmentPaymentMethod;
use Payone\Methods\PayonePayPalPaymentMethod;
use Payone\Methods\PayoneRatePayInstallmentPaymentMethod;
use Payone\Methods\PayoneSofortPaymentMethod;
use Plenty\Modules\Payment\Contracts\PaymentOrderRelationRepositoryContract;
use Plenty\Modules\Payment\Method\Contracts\PaymentMethodRepositoryContract;

/**
 * Class PaymentHelperTest
 */
class PaymentHelperTest extends \PHPUnit_Framework_TestCase
{
    /** @var PaymentHelper */
    private $helper;

    public function setUp()
    {
        $paymentMethodRepo = $this->createMock(PaymentMethodRepositoryContract::class);

        $paymentMethodRepo->method('allForPlugin')
            ->willReturn(
                [
                    (object)
                    [
                        'paymentKey' => PayoneInvoicePaymentMethod::PAYMENT_CODE,
                        'id' => 'invoice_mop',
                    ],
                    (object)
                    [
                        'paymentKey' => PayonePaydirektPaymentMethod::PAYMENT_CODE,
                        'id' => 'direct_mop',
                    ],
                    (object)
                    [
                        'paymentKey' => PayonePayolutionInstallmentPaymentMethod::PAYMENT_CODE,
                        'id' => 'inst_mop',
                    ],
                    (object)
                    [
                        'paymentKey' => PayonePayPalPaymentMethod::PAYMENT_CODE,
                        'id' => 'invoice_mop',
                    ],
                    (object)
                    [
                        'paymentKey' => PayoneRatePayInstallmentPaymentMethod::PAYMENT_CODE,
                        'id' => 'ratepay_inst_mop',
                    ],
                    (object)
                    [
                        'paymentKey' => PayoneSofortPaymentMethod::PAYMENT_CODE,
                        'id' => 'sofort_mop',
                    ],
                ]
            );
        $this->helper = new PaymentHelper(
            $paymentMethodRepo,
            self::createMock(PaymentOrderRelationRepositoryContract::class)
        );
    }

    public function testGetPaymentMethodMop()
    {
        $mop = $this->helper->getMopId(PayonePaydirektPaymentMethod::PAYMENT_CODE);

        $this->assertSame('direct_mop', $mop);
    }
}
