<?php

namespace Payone\Providers;

use Ceres\Helper\LayoutContainer;
use Payone\Adapter\Logger;
use Payone\Adapter\SessionStorage;
use Payone\Assistants\PayoneAssistant;
use Payone\Hooks\CopyPluginSetHook;
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
use Payone\Methods\PayoneKlarnaDirectBankTransferPaymentMethod;
use Payone\Methods\PayoneKlarnaDirectDebitPaymentMethod;
use Payone\Methods\PayoneKlarnaInstallmentsPaymentMethod;
use Payone\Methods\PayoneKlarnaInvoicePaymentMethod;
use Payone\Methods\PayonePaydirektPaymentMethod;
use Payone\Methods\PayonePayolutionInstallmentPaymentMethod;
use Payone\Methods\PayonePayPalPaymentMethod;
use Payone\Methods\PayonePrePaymentPaymentMethod;
use Payone\Methods\PayoneRatePayInstallmentPaymentMethod;
use Payone\Methods\PayoneSofortPaymentMethod;
use Payone\Models\Api\GenericPayment\ConfirmOrderReferenceResponse;
use Payone\Models\Api\GenericPayment\SetOrderReferenceDetailsResponse;
use Payone\Models\Api\GenericPayment\StartSessionResponse;
use Payone\Models\PaymentCache;
use Payone\Models\PaymentMethodContent;
use Payone\PluginConstants;
use Payone\Services\AmazonPayService;
use Payone\Services\OrderPdf;
use Payone\Services\PaymentCreation;
use Payone\Services\PaymentService;
use Payone\Services\SettingsService;
use Payone\Views\ErrorMessageRenderer;
use Payone\Views\PaymentRenderer;
use Plenty\Modules\Basket\Contracts\BasketRepositoryContract;
use Plenty\Modules\Basket\Events\Basket\AfterBasketChanged;
use Plenty\Modules\Basket\Events\Basket\AfterBasketCreate;
use Plenty\Modules\Basket\Events\BasketItem\AfterBasketItemAdd;
use Plenty\Modules\Basket\Models\Basket;
use Plenty\Modules\Document\Models\Document;
use Plenty\Modules\EventProcedures\Services\Entries\ProcedureEntry;
use Plenty\Modules\EventProcedures\Services\EventProceduresService;
use Plenty\Modules\Order\Models\Order;
use Plenty\Modules\Order\Contracts\OrderRepositoryContract;
use Plenty\Modules\Order\Property\Models\OrderPropertyType;
use Plenty\Modules\Payment\Contracts\PaymentRepositoryContract;
use Plenty\Modules\Payment\Events\Checkout\ExecutePayment;
use Plenty\Modules\Payment\Events\Checkout\GetPaymentMethodContent;
use Plenty\Modules\Payment\Method\Contracts\PaymentMethodContainer;
use Plenty\Modules\Payment\Models\Payment;
use Plenty\Modules\Plugin\Events\CopyPluginSet;
use Plenty\Modules\Wizard\Contracts\WizardContainerContract;
use Plenty\Plugin\Events\Dispatcher;
use Plenty\Plugin\ServiceProvider;
use Plenty\Modules\Order\Pdf\Events\OrderPdfGenerationEvent;
use Plenty\Plugin\Templates\Twig;
use Plenty\Plugin\Translation\Translator;
use Payone\Services\KlarnaService;

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

        $this->registerPaymentRendering($eventDispatcher, $basket);

        $this->registerPaymentExecute($eventDispatcher, $basket);

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

        $this->registerAmazonPayListener($eventDispatcher, $basket);

        $eventDispatcher->listen(CopyPluginSet::class, CopyPluginSetHook::class);

        pluginApp(WizardContainerContract::class)->register('payment-payone-assistant', PayoneAssistant::class);
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

        $payContainer->register(
            'Payone::' . PayoneKlarnaDirectBankTransferPaymentMethod::PAYMENT_CODE,
            PayoneKlarnaDirectBankTransferPaymentMethod::class,
            $events
        );

        $payContainer->register(
            'Payone::' . PayoneKlarnaDirectDebitPaymentMethod::PAYMENT_CODE,
            PayoneKlarnaDirectDebitPaymentMethod::class,
            $events
        );

        $payContainer->register(
            'Payone::' . PayoneKlarnaInstallmentsPaymentMethod::PAYMENT_CODE,
            PayoneKlarnaInstallmentsPaymentMethod::class,
            $events
        );

        $payContainer->register(
            'Payone::' . PayoneKlarnaInvoicePaymentMethod::PAYMENT_CODE,
            PayoneKlarnaInvoicePaymentMethod::class,
            $events
        );
    }

    /**
     * @param Dispatcher $eventDispatcher
     * @param BasketRepositoryContract $basketRepository
     */
    protected function registerPaymentRendering(
        Dispatcher $eventDispatcher,
        BasketRepositoryContract $basketRepository
    ) {
        $eventDispatcher->listen(
            GetPaymentMethodContent::class,
            function (GetPaymentMethodContent $event) use ($basketRepository) {
                /** @var SettingsService $settingsService */
                $settingsService = pluginApp(SettingsService::class);
                /** @var Logger $logger */
                $logger = pluginApp(Logger::class);
                /** @var PaymentHelper $paymentHelper */
                $paymentHelper = pluginApp(PaymentHelper::class);

                $basket = $basketRepository->load();

                $logger->setIdentifier(__METHOD__)->info('Event.getPaymentMethodContent');
                if (!$event->getMop() || !$paymentHelper->isPayonePayment($event->getMop())) {
                    return;
                } elseif ($event->getMop() == $paymentHelper->getMopId(PayoneAmazonPayPaymentMethod::PAYMENT_CODE)) {
                    $amazonPayActive = $settingsService->getPaymentSettingsValue('active', PayoneAmazonPayPaymentMethod::PAYMENT_CODE);
                    if (isset($amazonPayActive) && $amazonPayActive == 1) {
                        $this->registerAmazonPayIntegration($event, $basket);
                    }
                    return;
                }

                /** @var PaymentService $paymentService */
                $paymentService = pluginApp(PaymentService::class);
                $paymentCode = $paymentHelper->getPaymentCodeByMop($event->getMop());
                /** @var PaymentAbstract $payment */
                $payment = PaymentMethodServiceFactory::create($paymentCode);
                /** @var AddressHelper $addressHelper */
                $addressHelper = pluginApp(AddressHelper::class);

                $billingAddress = $addressHelper->getBasketBillingAddress($basket);
                if( $paymentCode == PayoneInvoiceSecurePaymentMethod::PAYMENT_CODE &&
                    (!isset($billingAddress->birthday) || !strlen($billingAddress->birthday)) ) {

                    /** @var Translator $translator */
                    $translator = pluginApp(Translator::class);
                    /** @var ShopHelper $shopHelper */
                    $shopHelper = pluginApp(ShopHelper::class);
                    $lang = $shopHelper->getCurrentLanguage();

                    $dateOfBirthMissingMessage = $translator->trans('Payone::Template.missingDateOfBirth', [], $lang);

                    $event->setValue($dateOfBirthMissingMessage);
                    $event->setType(GetPaymentMethodContent::RETURN_TYPE_ERROR);
                    return;
                }elseif ($paymentCode == PayoneKlarnaDirectDebitPaymentMethod::PAYMENT_CODE ||
                    $paymentCode == PayoneKlarnaInvoicePaymentMethod::PAYMENT_CODE ||
                    $paymentCode == PayoneKlarnaInstallmentsPaymentMethod::PAYMENT_CODE ||
                    $paymentCode == PayoneKlarnaDirectBankTransferPaymentMethod::PAYMENT_CODE) {

                    /** @var KlarnaService $klarnaService */
                    $klarnaService = pluginApp(KlarnaService::class);

                    /** @var StartSessionResponse $response */
                    $response = $klarnaService->startSession($paymentCode, $basket);

                    /** @var Twig $twig */
                    $twig = pluginApp(Twig::class);
                    $event->setValue($twig->render(
                        PluginConstants::NAME . '::Checkout.KlarnaWidget',
                        [
                            'client_token' => $response->getKlarnaClientToken(),
                            'payment_method' => $response->getKlarnaMethodIdentifier()
                        ]
                    ));
                    $event->setType(GetPaymentMethodContent::RETURN_TYPE_HTML);
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

    /**
     * @param Dispatcher $dispatcher
     * @param BasketRepositoryContract $basketRepository
     */
    protected function registerPaymentExecute(Dispatcher $dispatcher, BasketRepositoryContract $basketRepository)
    {
        $dispatcher->listen(ExecutePayment::class, function (ExecutePayment $event) use ($basketRepository){
            /** @var PaymentHelper $paymentHelper */
            $paymentHelper = pluginApp(PaymentHelper::class);
            /** @var Logger $logger */
            $logger = pluginApp(Logger::class);

            if($paymentHelper->isPayonePayment($event->getMop())) {
                try{
                    if($event->getMop() == $paymentHelper->getMopId(PayoneAmazonPayPaymentMethod::PAYMENT_CODE)) {
                        /** @var PaymentService $paymentService */
                        $paymentService = pluginApp(PaymentService::class);
                        $basket = $basketRepository->load();

                        $auth = $paymentService->openTransaction($basket);
                        $logger
                            ->setIdentifier(__METHOD__)
                            ->debug('AmazonPay.paymentExecute', [
                                "auth" => (array) $auth
                            ]);
                        /** @var SessionStorage $sessionStorage */
                        $sessionStorage = pluginApp(SessionStorage::class);
                        $sessionStorage->setSessionValue('clientId', null);
                        $sessionStorage->setSessionValue('sellerId', null);
                        $sessionStorage->setSessionValue('workOrderId', null);
                        $sessionStorage->setSessionValue('accessToken', null);
                    }
                    /** @var OrderRepositoryContract $orderRepository */
                    $orderRepository = pluginApp(OrderRepositoryContract::class);
                    /** @var PaymentCache $paymentCache */
                    $paymentCache = pluginApp(PaymentCache::class);

                    $order = $orderRepository->findOrderById($event->getOrderId());
                    if($order instanceof Order) {
                        if($event->getMop() == $paymentHelper->getMopId(PayoneInvoiceSecurePaymentMethod::PAYMENT_CODE)) {
                            //Block the invoice generation for secure invoice because there will be an external invoice
                            $orderRepository->updateOrder([
                                'properties' => [
                                    [
                                        'typeId' => OrderPropertyType::EXTERNAL_TAX_SERVICE,
                                        'value' => "1"
                                    ]
                                ]
                            ], $order->id);
                        }

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
                } catch (\Exception $exception){
                    $logger
                        ->setIdentifier(__METHOD__)
                        ->error('Error in paymentExecute-Event', $exception);
                }
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

                /** @var Order $order */
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

    /**
     * @param GetPaymentMethodContent $event
     * @param Basket $basket
     */
    protected function registerAmazonPayIntegration(GetPaymentMethodContent $event, Basket $basket)
    {
        /** @var AmazonPayService $amazonPayService */
        $amazonPayService = pluginApp(AmazonPayService::class);
        /** @var Logger $logger */
        $logger = pluginApp(Logger::class);
        /** @var Twig $twig */
        $twig = pluginApp(Twig::class);
        /** @var SessionStorage $sessionStorage */
        $sessionStorage = pluginApp(SessionStorage::class);
        /** @var PaymentCache $paymentCache */
        $paymentCache = pluginApp(PaymentCache::class);

        try {
            $paymentCache->setActiveBasketId($basket->id);

            /** @var SetOrderReferenceDetailsResponse $setOrderRefResponse */
            $setOrderRefResponse = $amazonPayService->setOrderReference($basket);
            /** @var ConfirmOrderReferenceResponse $confirmOrderRefResponse */
            $confirmOrderRefResponse = $amazonPayService->confirmOrderReference($basket);

            $event->setValue($twig->render(
                PluginConstants::NAME . '::Checkout.Confirmation',
                [
                    'success' => $confirmOrderRefResponse->getSuccess(),
                    'sellerId' => $sessionStorage->getSessionValue('sellerId'),
                    'amazonReferenceId' => $sessionStorage->getSessionValue('amazonReferenceId'),
                ]
            ));
            $event->setType(GetPaymentMethodContent::RETURN_TYPE_HTML);

            $logger
                ->setIdentifier(__METHOD__)
                ->debug('AmazonPay.paymentMethodContent', [
                    "event" => (array)$event,
                    "setOrderRefResponse" => (array)$setOrderRefResponse,
                    "confirmOrderRefResponse" => (array)$confirmOrderRefResponse
                ]);

        } catch (\Exception $exception) {
            $logger
                ->setIdentifier(__METHOD__)
                ->error('AmazonPay.paymentMethodContent', $exception);
        }
    }

    /**
     * @param Dispatcher $eventDispatcher
     * @param BasketRepositoryContract $basket
     */
    protected function registerAmazonPayListener(Dispatcher $eventDispatcher, BasketRepositoryContract $basket)
    {
        $eventDispatcher->listen(
            'IO.Resources.Import', function ($resourceContainer) use ($basket) {
            /** @var SettingsService $settingsService */
            $settingsService = pluginApp(SettingsService::class);
            if($settingsService->getPaymentSettingsValue('active', PayoneAmazonPayPaymentMethod::PAYMENT_CODE)) {
                /** @var PaymentHelper $twig */
                $paymentHelper = pluginApp(PaymentHelper::class);
                $amazonPayMopId = $paymentHelper->getMopId(PayoneAmazonPayPaymentMethod::PAYMENT_CODE);
                $basketData = $basket->load();
                $resourceContainer->addScriptTemplate(
                    PluginConstants::NAME . '::Checkout.AmazonPayCheckout', [
                        'selectedPaymentId' => $basketData->methodOfPaymentId,
                        'amazonPayMopId' => $amazonPayMopId,
                        'sandbox' => (bool)$settingsService->getPaymentSettingsValue('Sandbox', PayoneAmazonPayPaymentMethod::PAYMENT_CODE)
                ]);
            }
        });

        $eventDispatcher->listen(
            "Ceres.LayoutContainer.Checkout.BeforeBillingAddress",
            function (LayoutContainer $container) {
                /** @var SettingsService $settingsService */
                $settingsService = pluginApp(SettingsService::class);
                if($settingsService->getPaymentSettingsValue('active', PayoneAmazonPayPaymentMethod::PAYMENT_CODE)) {
                    /** @var Twig $twig */
                    $twig = pluginApp(Twig::class);
                    $container->addContent($twig->render(PluginConstants::NAME . '::Checkout.AmazonPayAddressBookWidget'));
                }
            }
        );
    }
}
