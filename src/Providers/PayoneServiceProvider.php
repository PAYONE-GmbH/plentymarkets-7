<?php

namespace Payone\Providers;

use Payone\Adapter\Logger;
use Payone\Helpers\PaymentHelper;
use Payone\Methods\PaymentAbstract;
use Payone\Methods\PaymentMethodServiceFactory;
use Payone\Methods\PayoneCODPaymentMethod;
use Payone\Methods\PayoneInvoicePaymentMethod;
use Payone\Methods\PayonePaydirektPaymentMethod;
use Payone\Methods\PayonePayolutionInstallmentPaymentMethod;
use Payone\Methods\PayonePayPalPaymentMethod;
use Payone\Methods\PayonePrePaymentPaymentMethod;
use Payone\Methods\PayoneRatePayInstallmentPaymentMethod;
use Payone\Methods\PayoneSofortPaymentMethod;
use Payone\Models\PaymentCache;
use Payone\Models\PaymentMethodContent;
use Payone\PluginConstants;
use Payone\Services\Capture;
use Payone\Services\PaymentCreation;
use Payone\Services\PaymentService;
use Payone\Views\ErrorMessageRenderer;
use Payone\Views\PaymentRenderer;
use Plenty\Log\Exceptions\ReferenceTypeException;
use Plenty\Log\Services\ReferenceContainer;
use Plenty\Modules\Basket\Contracts\BasketRepositoryContract;
use Plenty\Modules\Basket\Events\Basket\AfterBasketChanged;
use Plenty\Modules\Basket\Events\Basket\AfterBasketCreate;
use Plenty\Modules\Basket\Events\BasketItem\AfterBasketItemAdd;
use Plenty\Modules\EventProcedures\Services\Entries\ProcedureEntry;
use Plenty\Modules\EventProcedures\Services\EventProceduresService;
use Plenty\Modules\Order\Events\OrderCreated;
use Plenty\Modules\Order\Models\OrderType;
use Plenty\Modules\Payment\Events\Checkout\GetPaymentMethodContent;
use Plenty\Modules\Payment\Method\Contracts\PaymentMethodContainer;
use Plenty\Modules\Payment\Models\Payment;
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
     * @param ErrorMessageRenderer $errorMessageRenderer
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
        EventProceduresService $eventProceduresService,
        ErrorMessageRenderer $errorMessageRenderer,
        PaymentCreation $paymentCreationService,
        PaymentCache $paymentCache,
        ReferenceContainer $referenceContainer
    ) {
        $this->registerPaymentMethods($payContainer);

        $this->registerPaymentRendering(
            $eventDispatcher,
            $paymentHelper,
            $paymentService,
            $paymentRenderer,
            $content,
            $logger,
            $basket,
            $errorMessageRenderer
        );

        $this->registerOrderCreationEvents(
            $eventDispatcher,
            $paymentHelper,
            $logger,
            $paymentCreationService,
            $paymentCache
        );

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

        $this->registerReferenceTypesForLogging($referenceContainer);
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
        $payContainer->register(
            'Payone::' . PayonePrePaymentPaymentMethod::PAYMENT_CODE,
            PayonePrePaymentPaymentMethod::class,
            $events
        );
        $payContainer->register(
            'Payone::' . PayoneCODPaymentMethod::PAYMENT_CODE,
            PayoneCODPaymentMethod::class,
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
        BasketRepositoryContract $basketRepository,
        ErrorMessageRenderer $errorMessageRenderer
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
                $basketRepository,
                $errorMessageRenderer
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
                    $paymentService->openTransaction($basketRepository->load());
                } catch (\Exception $e) {
                    $errorMessage = $e->getMessage();
                    $logger->logException($e);
                    $event->setValue($errorMessageRenderer->render($errorMessage));
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
     * @param Logger $logger
     * @param Capture $captureService
     * @param PaymentCreation $paymentCreationService
     */
    private function registerOrderCreationEvents(
        Dispatcher $eventDispatcher,
        PaymentHelper $paymentHelper,
        Logger $logger,
        PaymentCreation $paymentCreationService,
        PaymentCache $paymentCache
    ) {
        $logger = $logger->setIdentifier(__METHOD__);
        $eventDispatcher->listen(OrderCreated::class,
            function (OrderCreated $event) use (
                $paymentHelper,
                $logger,
                $paymentCreationService,
                $paymentCache
            ) {
                $order = $event->getOrder();
                $logger->info('Event.orderCreated', [$order, $order->id]);
                if ($order->typeId != OrderType::TYPE_SALES_ORDER) {
                    return;
                }
                $selectedPaymentId = $order->methodOfPaymentId;
                if (!$selectedPaymentId || !$paymentHelper->isPayonePayment($selectedPaymentId)) {
                    return;
                }
                $payment = $paymentCache->loadPayment($selectedPaymentId);
                if (!($payment instanceof Payment)) {
                    $message = 'Payment could not be assigned to order.';
                    $logger->error($message, $payment);
                    throw new \Exception($message);
                }
                $paymentCreationService->assignPaymentToOrder($payment, $order);
                $paymentCache->deletePayment($selectedPaymentId);
            }
        );
    }

    /**
     * @param ReferenceContainer $referenceContainer
     */
    private function registerReferenceTypesForLogging(ReferenceContainer $referenceContainer)
    {
        try {
            $referenceContainer->add([Logger::PAYONE_REQUEST_REFERENCE => Logger::PAYONE_REQUEST_REFERENCE]);
        } catch (ReferenceTypeException $ex) {
            // already registered
        }
    }
}
