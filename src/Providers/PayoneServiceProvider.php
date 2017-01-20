<?php

namespace Payone\Providers;

use Payone\Helper\PaymentHelper;
use Payone\Methods\PayoneInvoicePaymentMethod;
use Payone\Methods\PayonePaydirektPaymentMethod;
use Payone\Methods\PayonePayolutionInstallmentPaymentMethod;
use Payone\Methods\PayonePayPalPaymentMethod;
use Payone\Methods\PayoneRatePayInstallmentPaymentMethod;
use Payone\Methods\PayoneSofortPaymentMethod;
use Payone\Services\PaymentService;
use Plenty\Modules\EventProcedures\Services\EventProceduresService;
use Plenty\Modules\Payment\Method\Contracts\PaymentMethodContainer;
use Plenty\Modules\Payment\Events\Checkout\GetPaymentMethodContent;
use Plenty\Modules\Basket\Contracts\BasketRepositoryContract;
use Plenty\Modules\Basket\Events\Basket\AfterBasketChanged;
use Plenty\Modules\Basket\Events\BasketItem\AfterBasketItemAdd;
use Plenty\Modules\Basket\Events\Basket\AfterBasketCreate;

use Plenty\Plugin\Events\Dispatcher;
use Plenty\Plugin\ServiceProvider;

class PayoneServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->getApplication()->register(PayoneRouteServiceProvider::class);
    }

    /**
     * Boot additional Payone services
     *
     * @param Dispatcher $eventDispatcher
     * @param PaymentHelper $paymentHelper
     * @param PaymentService $paymentService
     * @param BasketRepositoryContract $basket
     * @param PaymentMethodContainer $payContainer
     * @param EventProceduresService $eventProceduresService
     */
    public function boot(
        Dispatcher $eventDispatcher,
        PaymentHelper $paymentHelper,
        PaymentService $paymentService,
        BasketRepositoryContract $basket,
        PaymentMethodContainer $payContainer,
        EventProceduresService $eventProceduresService
    ) {
        $this->registerPaymentMethods($payContainer);
        $this->addPaymentMethodContent($eventDispatcher, $paymentHelper, $paymentService, $basket);
        $this->executePayment($eventDispatcher, $paymentHelper, $paymentService, $basket);


    }

    /**
     * @param PaymentMethodContainer $payContainer
     */
    protected function registerPaymentMethods(PaymentMethodContainer $payContainer)
    {
        $events = [AfterBasketChanged::class, AfterBasketItemAdd::class, AfterBasketCreate::class];

        $payContainer->register(
            'Payone::' . PayoneInvoicePaymentMethod::PAYMENT_CODE,
            PayoneInvoicePaymentMethod::class,
            $events
        );
        $payContainer->register(
            'Payone::' . PayonePaydirektPaymentMethod::PAYMENT_CODE,
            PayonePaydirektPaymentMethod::class,
            $events
        );
        $payContainer->register(
            'Payone::' . PayonePayolutionInstallmentPaymentMethod::PAYMENT_CODE,
            PayonePayolutionInstallmentPaymentMethod::class,
            $events
        );
        $payContainer->register(
            'Payone::' . PayonePayPalPaymentMethod::PAYMENT_CODE,
            PayonePayPalPaymentMethod::class,
            $events
        );
        $payContainer->register(
            'Payone::' . PayoneRatePayInstallmentPaymentMethod::PAYMENT_CODE,
            PayoneRatePayInstallmentPaymentMethod::class,
            $events
        );
        $payContainer->register(
            'Payone::' . PayoneSofortPaymentMethod::PAYMENT_CODE,
            PayoneSofortPaymentMethod::class,
            $events
        );
    }

    /**
     * @param Dispatcher $eventDispatcher
     * @param PaymentHelper $paymentHelper
     * @param PaymentService $paymentService
     * @param BasketRepositoryContract $basket
     * @return void
     */
    private function addPaymentMethodContent(
        Dispatcher $eventDispatcher,
        PaymentHelper $paymentHelper,
        PaymentService $paymentService,
        BasketRepositoryContract $basket
    ) {
        $eventDispatcher->listen(
            GetPaymentMethodContent::class,
            function (GetPaymentMethodContent $event) use ($paymentHelper, $basket, $paymentService) {
                if (in_array($event->getMop(), $paymentHelper->getPayoneMops())) {
                    $basket = $basket->load();

                    $event->setValue($paymentService->getPaymentContent($basket));
                    $event->setType($paymentService->getReturnType());
                }
            }
        );
    }

    /**
     * @param Dispatcher $eventDispatcher
     * @param PaymentHelper $paymentHelper
     * @param PaymentService $paymentService
     * @param BasketRepositoryContract $basket
     * @return void
     */
    private function executePayment(
        Dispatcher $eventDispatcher,
        PaymentHelper $paymentHelper,
        PaymentService $paymentService,
        BasketRepositoryContract $basket
    ) {

        // Listen for the event that executes the payment
        $eventDispatcher->listen(ExecutePayment::class,
            function (ExecutePayment $event) use ($paymentHelper, $paymentService) {
                if (!in_array($event->getMop(), $paymentHelper->getPayoneMops())) {
                    return;
                }

                $orderId = $event->getOrderId();
                // Execute the payment
                $payPalPaymentData = $paymentService->executePayment();

                // Check whether the PayPal payment has been executed successfully
                if ($paymentService->getReturnType() != 'errorCode') {
                    // Create a plentymarkets payment from the paypal execution params
                    $plentyPayment = $paymentHelper->createPlentyPayment($payPalPaymentData);

                    if ($plentyPayment instanceof Payment) {
                        // Assign the payment to an order in plentymarkets
                        $paymentHelper->assignPlentyPaymentToPlentyOrder($plentyPayment, $orderId);

                        $event->setType('success');
                        $event->setValue('The Payment has been executed successfully!');
                    }
                } else {
                    $event->setType('error');
                    $event->setValue('The PayPal-Payment could not be executed!');
                }
            }
        );
    }

}
