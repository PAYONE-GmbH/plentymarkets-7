<?php //strict

namespace Payone\Helper;

use Payone\Methods\PayoneInvoicePaymentMethod;
use Payone\Methods\PayonePaydirektPaymentMethod;
use Payone\Methods\PayonePayolutionInstallmentPaymentMethod;
use Payone\Methods\PayonePayPalPaymentMethod;
use Payone\Methods\PayoneRatePayInstallmentPaymentMethod;
use Payone\Methods\PayoneSofortPaymentMethod;
use Payone\Models\PayonePaymentStatus;
use Plenty\Modules\Payment\Contracts\PaymentRepositoryContract;
use Plenty\Modules\Payment\Method\Contracts\PaymentMethodRepositoryContract;
use Plenty\Modules\Payment\Models\Payment;
use Plenty\Modules\Payment\Models\PaymentProperty;

/**
 * Class PaymentHelper
 *
 * @package Payone\Helper
 */
class PaymentHelper
{

    /**
     * @var PaymentMethodRepositoryContract
     */
    private $paymentMethodRepo;
    /**
     * @var PaymentRepositoryContract
     */
    private $paymentRepository;

    /**
     * PaymentHelper constructor.
     *
     * @param PaymentMethodRepositoryContract $paymentMethodRepo
     * @param PaymentRepositoryContract $paymentRepository
     */
    public function __construct(
        PaymentMethodRepositoryContract $paymentMethodRepo,
        PaymentRepositoryContract $paymentRepository
    ) {
        $this->paymentMethodRepo = $paymentMethodRepo;
        $this->paymentRepository = $paymentRepository;
    }

    /**
     * Get the ID of the Payone payment method
     *
     * @param string $paymentCode
     * @return string
     */
    public function getPayoneMopId($paymentCode)
    {
        $paymentMethods = $this->paymentMethodRepo->allForPlugin('Payone');

        if (!$paymentMethods) {
            return 'no_paymentmethod_found';
        }
        foreach ($paymentMethods as $paymentMethod) {
            if ($paymentMethod->paymentKey == $paymentCode) {
                return $paymentMethod->id;
            }
        }

        return 'no_paymentmethod_found';
    }

    /**
     * @return array
     */
    public function getPayonePaymentCodes()
    {
        return [
            PayoneInvoicePaymentMethod::PAYMENT_CODE,
            PayonePaydirektPaymentMethod::PAYMENT_CODE,
            PayonePayolutionInstallmentPaymentMethod::PAYMENT_CODE,
            PayonePayPalPaymentMethod::PAYMENT_CODE,
            PayoneRatePayInstallmentPaymentMethod::PAYMENT_CODE,
            PayoneSofortPaymentMethod::PAYMENT_CODE,
        ];
    }

    /**
     * @return array
     */
    public function getPayoneMops()
    {
        $mops = [];
        foreach ($this->getPayonePaymentCodes() as $paymentCode) {
            $mops[] = $this->getPayoneMopId($paymentCode);
        }
        return $mops;
    }

    /**
     * @param $orderId
     * @param $txid
     * @param string $txaction
     * @return void
     */
    public function updatePaymentStatus($orderId, $txid, $txaction)
    {
        $payments = $this->paymentRepository->getPaymentsByOrderId($orderId);

        /* @var $payment Payment */
        foreach ($payments as $payment) {
            /* @var $property PaymentProperty */
            foreach ($payment->property as $property) {
                if ($property->typeId === 30 && $property->id === $txid) {
                    $payment->status = PayonePaymentStatus::getPlentyStatus($txaction);
                    $this->paymentRepository->updatePayment($payment);
                }
            }
        }
    }
}
