<?php

namespace Payone\Providers;

use Payone\Adapter\Logger;
use Payone\Helpers\AddressHelper;
use Payone\Helpers\OrderHelper;
use Payone\Helpers\PaymentHelper;
use Payone\Helpers\ShopHelper;
use Payone\Methods\PaymentAbstract;
use Payone\Methods\PaymentMethodServiceFactory;
use Payone\Methods\PayoneAmazonPayPaymentMethod;
use Payone\Methods\PayoneCCPaymentMethod;
use Payone\Methods\PayoneCODPaymentMethod;
use Payone\Methods\PayoneDirectDebitPaymentMethod;
use Payone\Methods\PayoneInvoicePaymentMethod;
use Payone\Methods\PayoneInvoiceSecurePaymentMethod;
use Payone\Methods\PayonePaydirektPaymentMethod;
use Payone\Methods\PayonePayolutionInstallmentPaymentMethod;
use Payone\Methods\PayonePayPalPaymentMethod;
use Payone\Methods\PayonePrePaymentPaymentMethod;
use Payone\Methods\PayoneRatePayInstallmentPaymentMethod;
use Payone\Methods\PayoneSofortPaymentMethod;
use Payone\Models\Api\GenericPayment\ConfirmOrderReferenceResponse;
use Payone\Models\Api\GenericPayment\SetOrderReferenceDetailsResponse;
use Payone\Models\PaymentCache;
use Payone\Models\PaymentMethodContent;
use Payone\PluginConstants;
use Payone\Services\AmazonPayService;
use Payone\Services\Capture;
use Payone\Services\OrderPdf;
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
use Plenty\Modules\Basket\Models\Basket;
use Plenty\Modules\Document\Models\Document;
use Plenty\Modules\EventProcedures\Services\Entries\ProcedureEntry;
use Plenty\Modules\EventProcedures\Services\EventProceduresService;
use Plenty\Modules\Order\Contracts\OrderRepositoryContract;
use Plenty\Modules\Order\Events\OrderCreated;
use Plenty\Modules\Order\Models\OrderType;
use Plenty\Modules\Payment\Contracts\PaymentRepositoryContract;
use Plenty\Modules\Payment\Events\Checkout\ExecutePayment;
use Plenty\Modules\Payment\Events\Checkout\GetPaymentMethodContent;
use Plenty\Modules\Payment\Method\Contracts\PaymentMethodContainer;
use Plenty\Modules\Payment\Models\Payment;
use Plenty\Plugin\Events\Dispatcher;
use Plenty\Plugin\ServiceProvider;
use Plenty\Modules\Order\Pdf\Events\OrderPdfGenerationEvent;

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
     * @param BasketRepositoryContract $basket
     * @param PaymentMethodContainer $payContainer
     * @param EventProceduresService $eventProceduresService
     */
    public function boot(
        Dispatcher $eventDispatcher,
        BasketRepositoryContract $basket,
        PaymentMethodContainer $payContainer,
        EventProceduresService $eventProceduresService
    ) {
        $this->registerPaymentMethods($payContainer);

        $this->registerAmazonPayIntegration($eventDispatcher, $basket);

        $this->registerPaymentRendering(
            $eventDispatcher,
            $basket
        );

        $this->registerPaymentExecute($eventDispatcher);

        $captureProcedureTitle = [
            'de' => 'Versandbestätigung an ' . PluginConstants::NAME,
            'en' => 'Send shipping confirmation to ' . PluginConstants::NAME,
        ];
        $eventProceduresService->registerProcedure(
            PluginConstants::NAME,
            ProcedureEntry::EVENT_TYPE_ORDER,
            $captureProcedureTitle,
            '\Payone\Procedures\CaptureEventProcedure@run'
        );

        $refundProcedureTitle = [
            'de' => PluginConstants::NAME . ' | Rückerstattung senden',
            'en' => PluginConstants::NAME . ' | Refund order',
        ];
        $eventProceduresService->registerProcedure(
            PluginConstants::NAME,
            ProcedureEntry::EVENT_TYPE_ORDER,
            $refundProcedureTitle,
            '\Payone\Procedures\RefundEventProcedure@run'
        );

        $this->registerInvoicePdfGeneration($eventDispatcher);
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
        $payContainer->register(
            'Payone::' . PayoneCCPaymentMethod::PAYMENT_CODE,
            PayoneCCPaymentMethod::class,
            $events
        );
        $payContainer->register(
            'Payone::' . PayoneDirectDebitPaymentMethod::PAYMENT_CODE,
            PayoneDirectDebitPaymentMethod::class,
            $events
        );

        $payContainer->register(
            'Payone::' . PayoneInvoiceSecurePaymentMethod::PAYMENT_CODE,
            PayoneInvoiceSecurePaymentMethod::class,
            $events
        );

        $payContainer->register(
            'Payone::' . PayoneAmazonPayPaymentMethod::PAYMENT_CODE,
            PayoneAmazonPayPaymentMethod::class,
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
    protected function registerPaymentRendering(
        Dispatcher $eventDispatcher,
        BasketRepositoryContract $basketRepository
    ) {
        $eventDispatcher->listen(
            GetPaymentMethodContent::class,
            function (GetPaymentMethodContent $event) use ($basketRepository) {
                /** @var PaymentService $paymentService */
                $paymentService = pluginApp(PaymentService::class);
                /** @var Logger $logger */
                $logger = pluginApp(Logger::class);
                /** @var PaymentHelper $paymentHelper */
                $paymentHelper = pluginApp(PaymentHelper::class);

                $logger->setIdentifier(__METHOD__)->info('Event.getPaymentMethodContent');
                $selectedPaymentMopId = $event->getMop();
                if (!$selectedPaymentMopId || !$paymentHelper->isPayonePayment($selectedPaymentMopId)) {
                    return;
                }
                $paymentCode = $paymentHelper->getPaymentCodeByMop($selectedPaymentMopId);
                /** @var PaymentAbstract $payment */
                $payment = PaymentMethodServiceFactory::create($paymentCode);

                $basket = $basketRepository->load();
                /** @var AddressHelper $addressHelper */
                $addressHelper = pluginApp(AddressHelper::class);
                $billingAddress = $addressHelper->getBasketBillingAddress($basket);
                if( $paymentCode == PayoneInvoiceSecurePaymentMethod::PAYMENT_CODE &&
                    (!isset($billingAddress->birthday) || !strlen($billingAddress->birthday)) ) {

                    /** @var \Plenty\Plugin\Translation\Translator $translator */
                    $translator = pluginApp(\Plenty\Plugin\Translation\Translator::class);
                    /** @var ShopHelper $shopHelper */
                    $shopHelper = pluginApp(ShopHelper::class);
                    $lang = $shopHelper->getCurrentLanguage();

                    $dateOfBirthMissingMessage = $translator->trans('Payone::Template.missingDateOfBirth', [], $lang);

                    $event->setValue($dateOfBirthMissingMessage);
                    $event->setType(GetPaymentMethodContent::RETURN_TYPE_ERROR);
                    return;
                }

                try {
                    /** @var PaymentMethodContent $content */
                    $content = pluginApp(PaymentMethodContent::class);
                    $renderingType = $content->getPaymentContentType($paymentCode);

                    /** @var PaymentRenderer $paymentRenderer */
                    $paymentRenderer = pluginApp(PaymentRenderer::class);

                    $event->setType($renderingType);
                    switch ($renderingType) {
                        case GetPaymentMethodContent::RETURN_TYPE_REDIRECT_URL:
                            $auth = $paymentService->openTransaction($basket);
                            $event->setValue($auth->getRedirecturl());
                            break;
                        case GetPaymentMethodContent::RETURN_TYPE_CONTINUE:
                            $paymentService->openTransaction($basket);
                            break;
                        case  GetPaymentMethodContent::RETURN_TYPE_HTML:
                            $event->setValue($paymentRenderer->render($payment, ''));
                            break;
                    }
                } catch (\Exception $e) {
                    $errorMessage = $e->getMessage();
                    $logger->logException($e);

                    /** @var ErrorMessageRenderer $errorMessageRenderer */
                    $errorMessageRenderer = pluginApp(ErrorMessageRenderer::class);
                    $event->setValue($errorMessageRenderer->render($errorMessage));
                    $event->setType(GetPaymentMethodContent::RETURN_TYPE_ERROR);
                }
            }
        );
    }

    protected function registerPaymentExecute(Dispatcher $dispatcher)
    {
        $dispatcher->listen(ExecutePayment::class, function (ExecutePayment $event) {
            /** @var PaymentHelper $paymentHelper */
            $paymentHelper = pluginApp(PaymentHelper::class);
            if($paymentHelper->isPayonePayment($event->getMop())) {
                /** @var OrderRepositoryContract $orderRepository */
                $orderRepository = pluginApp(OrderRepositoryContract::class);
                /** @var PaymentCache $paymentCache */
                $paymentCache = pluginApp(PaymentCache::class);

                $order = $orderRepository->findOrderById($event->getOrderId());
                $payment = $paymentCache->loadPayment($event->getMop());
                if (!($payment instanceof Payment)) {
                    $message = 'Payment could not be assigned to order.';

                    /** @var Logger $logger */
                    $logger = pluginApp(Logger::class);
                    $logger->error($message, $payment);
                    return;
                }

                /** @var PaymentCreation $paymentCreationService */
                $paymentCreationService = pluginApp(PaymentCreation::class);
                $paymentCreationService->assignPaymentToOrder($payment, $order);
                $paymentCache->deletePayment($event->getMop());
            }
        });

    }

    /**
     * @param Dispatcher $eventDispatcher
     */
    protected function registerInvoicePdfGeneration(Dispatcher $eventDispatcher)
    {
        // Listen for the document generation event
        $eventDispatcher->listen(OrderPdfGenerationEvent::class,
            function (OrderPdfGenerationEvent $event) {

                /** @var PaymentHelper $paymentHelper */
                $paymentHelper = pluginApp(PaymentHelper::class);

                /** @var OrderHelper $orderHelper */
                $orderHelper = pluginApp(OrderHelper::class);

                /** @var \Order $order */
                $order = $event->getOrder();

                /** @var Logger $logger */
                $logger = pluginApp(Logger::class);
                $logger->setIdentifier(__METHOD__)->info(
                    'Event.orderPdfGeneration',
                    ['order' => $order->id, 'documentType' => $event->getDocType()]
                );
                if ($event->getDocType() != Document::INVOICE) {
                    return;
                }
                /** @var PaymentRepositoryContract $paymentRepository */
                $paymentRepository = pluginApp(PaymentRepositoryContract::class);

                try {
                    $payments = $paymentRepository->getPaymentsByOrderId($order->id);
                } catch (\Exception $e) {
                    $logger->error('Error loading payment', $e->getMessage());

                    return;
                }
                if (!($payments)) {
                    return;
                }
                $lang = $orderHelper->getLang($order);
                try {
                    /** @var OrderPdf $orderPdf */
                    $orderPdf = pluginApp(OrderPdf::class);
                    $orderPdfGenerationModel = $orderPdf->createPdfNote($payments[0], $lang);
                } catch (\Exception $e) {
                    $logger->error('Adding PDF comment failed for order '
                        . $order->id, $e->getMessage());

                    return;
                }
                if(!$orderPdfGenerationModel){
                    return;
                }
                $logger->setIdentifier(__METHOD__)->info(
                    'Event.orderPdfGeneration',
                    ['order' => $order->id, 'pdfData' => $orderPdfGenerationModel]
                );
                $event->addOrderPdfGeneration($orderPdfGenerationModel);
            }
        );
    }

    public function registerAmazonPayIntegration(Dispatcher $eventDispatcher, BasketRepositoryContract $basketRepository, AmazonPayService $amazonPayService)
    {
        $eventDispatcher->listen(GetPaymentMethodContent::class, function (GetPaymentMethodContent $event) use ($basketRepository, $amazonPayService) {
            /** @var Logger $logger */
            $logger = pluginApp(Logger::class);
            /** @var PaymentHelper $paymentHelper */
            $paymentHelper = pluginApp(PaymentHelper::class);
            if($event->getMop() == $paymentHelper->getMopId(PayoneAmazonPayPaymentMethod::PAYMENT_CODE)) {

                /** @var Basket $basket */
                $basket = $basketRepository->load();

                /** @var SetOrderReferenceDetailsResponse $setOrderRefResponse */
                $setOrderRefResponse = $amazonPayService->setOrderReference($basket);

                /** @var ConfirmOrderReferenceResponse $confirmOrderRefResponse */
                $confirmOrderRefResponse = $amazonPayService->confirmOrderReference($basket);

                $logger
                    ->setIdentifier(__METHOD__)
                    ->debug('AmazonPay.paymentMethodContent', [
                        "event" => (array) $event,
                        "setOrderRefResponse" => (array) $setOrderRefResponse,
                        "confirmOrderRefResponse" => (array) $confirmOrderRefResponse
                    ]);


                if($confirmOrderRefResponse->getSuccess() == true) {
                    $content = "OffAmazonPayments.initConfirmationFlow(sellerId, id, function(confirmationFlow) {confirmationFlow.success();});";
                } else {
                    $content = "OffAmazonPayments.initConfirmationFlow(sellerId, id, function(confirmationFlow) {confirmationFlow.error();});";
                }

                $event->setValue($content);
                $event->setType(GetPaymentMethodContent::RETURN_TYPE_HTML);
            }
        });
    }
}
