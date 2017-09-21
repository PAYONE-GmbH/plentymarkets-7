<?php

namespace Payone\Providers;

use Payone\Adapter\Logger;
use Payone\Helpers\PaymentHelper;
use Payone\Methods\PaymentAbstract;
use Payone\Methods\PaymentMethodServiceFactory;
use Payone\Methods\PayoneInvoicePaymentMethod;
use Payone\Methods\PayonePaydirektPaymentMethod;
use Payone\Methods\PayonePayolutionInstallmentPaymentMethod;
use Payone\Methods\PayonePayPalPaymentMethod;
use Payone\Methods\PayoneRatePayInstallmentPaymentMethod;
use Payone\Methods\PayoneSofortPaymentMethod;
use Payone\Models\PaymentMethodContent;
use Payone\PluginConstants;
use Payone\Services\PaymentService;
use Payone\Views\PaymentRenderer;
use Plenty\Modules\Basket\Contracts\BasketRepositoryContract;
use Plenty\Modules\Basket\Events\Basket\AfterBasketChanged;
use Plenty\Modules\Basket\Events\Basket\AfterBasketCreate;
use Plenty\Modules\Basket\Events\BasketItem\AfterBasketItemAdd;
use Plenty\Modules\Basket\Models\Basket;
use Plenty\Modules\EventProcedures\Services\Entries\ProcedureEntry;
use Plenty\Modules\EventProcedures\Services\EventProceduresService;
use Plenty\Modules\Payment\Events\Checkout\ExecutePayment;
use Plenty\Modules\Payment\Events\Checkout\GetPaymentMethodContent;
use Plenty\Modules\Payment\Method\Contracts\PaymentMethodContainer;
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
     * @param Dispatcher $eventDispatcher
     * @param PaymentHelper $paymentHelper
     * @param PaymentService $paymentService
     * @param BasketRepositoryContract $basket
     * @param PaymentMethodContainer $payContainer
     * @param PaymentRenderer $paymentRenderer
     * @param PaymentMethodContent $content
     * @param Logger $logger
     * @param EventProceduresService $eventProceduresService
     */
    public function boot(
        Dispatcher $eventDispatcher,
        PaymentHelper $paymentHelper,
        PaymentService $paymentService,
        BasketRepositoryContract $basket,
        PaymentMethodContainer $payContainer,
        PaymentRenderer $paymentRenderer,
        PaymentMethodContent $content,
        Logger $logger,
        EventProceduresService $eventProceduresService
    ) {
        $this->registerPaymentMethods($payContainer);


        $this->registerPaymentRendering(
            $eventDispatcher,
            $paymentHelper,
            $paymentService,
            $paymentRenderer,
            $content,
            $logger,
            $basket->load()
        );
        /*
        $this->subscribeExecutePayment($eventDispatcher, $paymentHelper, $paymentService, $basket->load());
        */

        $captureProcedureTitle = [
            'de' => PluginConstants::NAME . ' | Bestellung erfassen',
            'en' => PluginConstants::NAME . ' | Capture order',
        ];
        $eventProceduresService->registerProcedure(
            PluginConstants::NAME,
            ProcedureEntry::EVENT_TYPE_ORDER,
            $captureProcedureTitle,
            '\Payone\Procedures\CaptureEventProcedure@run'
        );

        $refundProcedureTitle = [
            'de' => PluginConstants::NAME . ' | Gutschrift erstellen',
            'en' => PluginConstants::NAME . ' | Refund order',
        ];
        $eventProceduresService->registerProcedure(
            PluginConstants::NAME,
            ProcedureEntry::EVENT_TYPE_ORDER,
            $refundProcedureTitle,
            '\Payone\Procedures\RefundEventProcedure@run'
        );
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
     * @param PaymentRenderer $paymentRenderer
     * @param PaymentMethodContent $content
     * @param Logger $logger
     */
    private function registerPaymentRendering(
        Dispatcher $eventDispatcher,
        PaymentHelper $paymentHelper,
        PaymentService $paymentService,
        PaymentRenderer $paymentRenderer,
        PaymentMethodContent $content,
        Logger $logger,
        Basket $basket
    ) {
        $logger = $logger->setIdentifier(__METHOD__);
        $eventDispatcher->listen(
            GetPaymentMethodContent::class,
            function (GetPaymentMethodContent $event) use (
                $paymentService,
                $paymentHelper,
                $paymentRenderer,
                $content,
                $logger,
                $basket
            ) {
                $logger->setIdentifier(__METHOD__)->info('Event.getPaymentMethodContent');
                $selectedPaymentMopId = $event->getMop();
                if (!$selectedPaymentMopId || !$paymentHelper->isPayonePayment($selectedPaymentMopId)) {
                    return;
                }
                $paymentCode = $paymentHelper->getPaymentCodeByMop($selectedPaymentMopId);
                /** @var PaymentAbstract $payment */
                $payment = PaymentMethodServiceFactory::create($paymentCode);

                try {
                    $paymentService->openTransaction($basket);
                } catch (\Exception $e) {
                    $errorMessage = $e->getMessage();
                    $event->setValue($paymentRenderer->render($payment, $errorMessage));
                    $event->setType(GetPaymentMethodContent::RETURN_TYPE_ERROR);

                    return;
                }
                $event->setType($content->getPaymentContentType($paymentCode));
                $event->setValue($paymentRenderer->render($payment, ''));
            }
        );
    }

    /**
     * @param Dispatcher $eventDispatcher
     * @param PaymentHelper $paymentHelper
     * @param PaymentService $paymentService
     * @param Basket $basket
     */
    private function subscribeExecutePayment(
        Dispatcher $eventDispatcher,
        PaymentHelper $paymentHelper,
        PaymentService $paymentService,
        Basket $basket
    ) {
        // Listen for the event that executes the payment
        $eventDispatcher->listen(
            ExecutePayment::class,
            function (ExecutePayment $event) use ($paymentHelper, $paymentService, $basket) {
                if (!in_array($event->getMop(), $paymentHelper->getMops())) {
                    return;
                }
            }
        );
    }
}
