<?php //strict

namespace Payone\Helper;

use Payone\Methods\PayoneCODPaymentMethod;
use Payone\Methods\PayoneInvoicePaymentMethod;
use Payone\Methods\PayonePaydirektPaymentMethod;
use Payone\Methods\PayonePayolutionInstallmentPaymentMethod;
use Payone\Methods\PayonePayPalPaymentMethod;
use Payone\Methods\PayonePrePaymentPaymentMethod;
use Payone\Methods\PayoneRatePayInstallmentPaymentMethod;
use Payone\Methods\PayoneSofortPaymentMethod;
use Payone\Models\PayonePaymentStatus;
use Payone\PluginConstants;
use Plenty\Modules\Payment\Contracts\PaymentRepositoryContract;
use Plenty\Modules\Payment\Method\Contracts\PaymentMethodRepositoryContract;
use Plenty\Modules\Payment\Models\Payment;
use Plenty\Modules\Payment\Models\PaymentProperty;
use Plenty\Plugin\ConfigRepository;

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
     * @var ConfigRepository
     */
    private $config;

    /**
     * PaymentHelper constructor.
     *
     * @param PaymentMethodRepositoryContract $paymentMethodRepo
     * @param PaymentRepositoryContract $paymentRepository
     * @param ConfigRepository $config
     */
    public function __construct(
        PaymentMethodRepositoryContract $paymentMethodRepo,
        PaymentRepositoryContract $paymentRepository,
        ConfigRepository $config
    ) {
        $this->paymentMethodRepo = $paymentMethodRepo;
        $this->paymentRepository = $paymentRepository;
        $this->config = $config;
    }

    /**
     * Get the ID of the payment method
     *
     * @param string $paymentCode
     * @return string
     */
    public function getPayoneMopId($paymentCode)
    {
        $paymentMethods = $this->paymentMethodRepo->allForPlugin(PluginConstants::NAME);

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
            PayoneCODPaymentMethod::PAYMENT_CODE,
            PayonePrePaymentPaymentMethod::PAYMENT_CODE,
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

    /**
     * @param string $paymentCode
     * @return array
     */
    public function getApiContextParams($paymentCode)
    {
        $apiContextParams = [];

        $apiContextParams['aid'] = $this->config->get(PluginConstants::NAME . '.aid');
        $apiContextParams['mid'] = $this->config->get(PluginConstants::NAME . '.mid');
        $apiContextParams['portalid'] = $this->config->get(PluginConstants::NAME . '.portalid');
        $apiContextParams['key'] = $this->config->get(PluginConstants::NAME . '.key');
        $mode = $this->config->get(PluginConstants::NAME . '.mode');
        $apiContextParams['mode'] = ($mode == 1) ? 'test':'live';

        if ($this->config->get(PluginConstants::NAME . '.' . $paymentCode . '.useGlobalConfig')) {
            $apiContextParams['mode'] = $this->config->get(PluginConstants::NAME . '.mode');
        }

        return $apiContextParams;
    }
}
