<?php

namespace Payone\Controllers;

use IO\Services\BasketService;
use IO\Services\CheckoutService;
use Payone\Adapter\Logger;
use Payone\Adapter\SessionStorage;
use Payone\Helpers\PaymentHelper;
use Payone\Methods\PayoneAmazonPayPaymentMethod;
use Payone\Models\Api\GenericPayment\GetConfigurationResponse;
use Payone\Models\Api\GenericPayment\GetOrderReferenceDetailsResponse;
use Payone\Models\Api\GenericPayment\SetOrderReferenceDetailsResponse;
use Payone\PluginConstants;
use Payone\Providers\Api\Request\GenericPaymentDataProvider;
use Payone\Providers\Api\Request\Models\GenericPayment;
use Payone\Services\AmazonPayService;
use Payone\Services\Api;
use PayoneApi\Request\GenericPayment\AmazonPayGetOrderReferenceRequest;
use Plenty\Modules\Basket\Contracts\BasketRepositoryContract;
use Plenty\Modules\Basket\Models\Basket;
use Plenty\Modules\Frontend\Contracts\Checkout;
use Plenty\Modules\Webshop\Contracts\ContactRepositoryContract;
use Plenty\Modules\Webshop\Contracts\LocalizationRepositoryContract;
use Plenty\Plugin\Controller;
use Plenty\Plugin\Http\Request;
use Plenty\Plugin\Http\Response;
use Plenty\Plugin\Log\Loggable;
use Plenty\Plugin\Templates\Twig;


class AmazonPayController extends Controller
{

    /** @var Api */
    private $api;

    /** @var GenericPaymentDataProvider */
    private $dataProvider;

    /** @var Logger */
    private $logger;

    /**
     * AmazonPayController constructor.
     * @param Api $api
     * @param GenericPaymentDataProvider $dataProvider
     * @param Logger $logger
     */
    public function __construct(Api $api,
                                GenericPaymentDataProvider $dataProvider,
                                Logger $logger)
    {
        $this->api = $api;
        $this->dataProvider = $dataProvider;
        $this->logger = $logger;
    }

    public function getAmazonPayLoginWidget(Twig $twig,
                                            SessionStorage $sessionStorage,
                                            BasketRepositoryContract $basketRepository,
                                            PaymentHelper $paymentHelper)
    {
        $basket = $basketRepository->load();
        $selectedPaymentId = $basket->methodOfPaymentId;
        $amazonPayMopId = $paymentHelper->getMopId(PayoneAmazonPayPaymentMethod::PAYMENT_CODE);

        $requestParams = $this->dataProvider->getGetConfigRequestData(PayoneAmazonPayPaymentMethod::PAYMENT_CODE);

        /** @var GetConfigurationResponse $configResponse */
        $configResponse = $this->api->doGenericPayment(GenericPayment::ACTIONTYPE_GETCONFIGURATION, $requestParams);

        $sessionStorage->setSessionValue('clientId', $configResponse->getClientId());
        $sessionStorage->setSessionValue('sellerId', $configResponse->getSellerId());

        $this->logger
            ->setIdentifier(__METHOD__)
            ->debug('AmazonPay.configLoginButton', (array)$configResponse);

        /** @var LocalizationRepositoryContract $localizationRepositoryContract */
        $localizationRepositoryContract = pluginApp(LocalizationRepositoryContract::class);
        $lang = $this->getLanguageCode($localizationRepositoryContract->getLanguage());

        $content = [
            'clientId' => $configResponse->getClientId(),
            'sellerId' => $configResponse->getSellerId(),
            'type' => "LwA",
            'color' => "Gold",
            'size' => "medium",
            'language' => $lang,
            'scopes' => "profile payments:widget payments:shipping_address payments:billing_address",
            'popup' => "true",
            'workOrderId' => $configResponse->getWorkorderId()
        ];

        $this->logger
            ->setIdentifier(__METHOD__)
            ->debug('AmazonPay.renderLoginWidget', [
                "content" => (array)$content
            ]);

        return $twig->render(PluginConstants::NAME . '::Checkout.AmazonPayLogin', [
            'selectedPaymentId' => $selectedPaymentId,
            'amazonPayMopId' => $amazonPayMopId,
            'content' => $content
        ]);
    }


    public function renderWidgets(Twig $twig,
                                  PaymentHelper $paymentHelper,
                                  BasketRepositoryContract $basketRepository,
                                  Request $request,
                                  SessionStorage $sessionStorage)
    {
        $basket = $basketRepository->load();

        // AccessToken in Request
        $accessToken = $request->get('accessToken');
        $workdOrderId = $request->get('workOrderId');

        $sessionStorage->setSessionValue('accessToken', $accessToken);
        $sessionStorage->setSessionValue('workOrderId', $workdOrderId);

        // SWAP containers here
        $content = [
            'clientId' => "amzn1.application-oa2-client.2c027e55b128457bb16edc2f0fc6bd71",
            'sellerId' => "A13SNST9X74Q8L",
            'addressBookScope' => "profile payments:widget payments:shipping_address payments:billing_address",
            'walletScope' => "profile payments:widget payments:shipping_address payments:billing_address",
            'currency' => $basket->currency
        ];
        $amazonPayMopId = $paymentHelper->getMopId(PayoneAmazonPayPaymentMethod::PAYMENT_CODE);

        $this->logger
            ->setIdentifier(__METHOD__)
            ->debug('AmazonPay.renderWidgets', [
                "content" => (array)$content
            ]);

        return $twig->render(PluginConstants::NAME . '::Checkout.AmazonPayWidgets', [
            'content' => $content,
            'accessToken' => $accessToken,
            'workOrderId' => $workdOrderId,
            'amazonPayMopId' => $amazonPayMopId
        ]);
    }

    public function getOrderReference(Request $request, Response $response, Checkout $checkout, SessionStorage $sessionStorage)
    {
        try{
            $amazonReferenceId = $request->get('amazonReferenceId');
            $sessionStorage->setSessionValue('amazonReferenceId', $amazonReferenceId);

            $workOrderId = $sessionStorage->getSessionValue('workOrderId');
            $accessToken = $sessionStorage->getSessionValue('accessToken');

            /** @var GenericPaymentDataProvider $genericPaymentDataProvider */
            $genericPaymentDataProvider = pluginApp(GenericPaymentDataProvider::class);
            $requestParams = $genericPaymentDataProvider->getGetOrderReferenceDetailsRequestData(
                PayoneAmazonPayPaymentMethod::PAYMENT_CODE,
                $workOrderId,
                $accessToken,
                $amazonReferenceId
            );

            /** @var GetOrderReferenceDetailsResponse $orderReferenceResponse */
            $orderReferenceResponse = $this->api->doGenericPayment(GenericPayment::ACTIONTYPE_GETORDERREFERENCEDETAILS, $requestParams);

            $this->logger
                ->setIdentifier(__METHOD__)
                ->debug('AmazonPay.getOrderReference', [
                    "workOrderId" => $workOrderId,
                    "amazonReferenceId" => $amazonReferenceId,
                    "accessToken" => $accessToken,
                    "requestParams" => $requestParams,
                    "orderReferenceResponse" => (array)$orderReferenceResponse
                ]);


            /** @var AmazonPayService $amazonPayService */
            $amazonPayService = pluginApp(AmazonPayService::class);
            $shippingAddress = $amazonPayService->registerCustomerFromAmazonPay($orderReferenceResponse);
            $billingAddress = $amazonPayService->registerCustomerFromAmazonPay($orderReferenceResponse, true);


            $checkout->setCustomerInvoiceAddressId($shippingAddress->id);
            $checkout->setCustomerShippingAddressId($billingAddress->id);

            /** @var BasketService $basketService */
            $basketService = pluginApp(BasketService::class);

            /** @var ContactRepositoryContract $contactRepository */
            $contactRepository = pluginApp(ContactRepositoryContract::class);

            /** @var CheckoutService $checkoutService */
            $checkoutService = pluginApp(CheckoutService::class);
            $checkoutService->setBillingAddressId($shippingAddress->id);
            $checkoutService->setDeliveryAddressId($shippingAddress->id);

            $responseData['events']['AfterBasketChanged']['basket'] = $basketService->getBasketForTemplate();
            $responseData['events']['AfterBasketChanged']['showNetPrices'] = $contactRepository->showNetPrices();
            $responseData['events']['AfterBasketChanged']['basketItems'] = $basketService->getBasketItemsForTemplate(
                '',
                false
            );
            $responseData['events']['CheckoutChanged']['checkout'] = $checkoutService->getCheckout();

            $this->logger
                ->setIdentifier(__METHOD__)
                ->debug('AmazonPay.getOrderReference', [
                    "shippingAddress" => (array) $shippingAddress,
                    "checkout" => (array) $checkout,
                    "checkoutViaService" => (array)$checkoutService->getCheckout()
                ]);

            return $response->make(json_encode($responseData), 200);

            //return $response->json(['success' => true, 'message' => "Address changed", 'data' => $responseData]);


            //$checkout->setCustomerShippingAddressId($billingAddress->id);
        } catch (\Exception $exception) {
            $this->logger
                ->setIdentifier(__METHOD__)
                ->error('AmazonPay.getOrderReference', $exception);
        }

    }


    public function debugTest(BasketRepositoryContract $basketRepo)
    {
        /** @var AmazonPayService $apiDebug */
        $apiDebug = pluginApp(AmazonPayService::class);

        $basket = $basketRepo->load();

        $confirmOrderResponse = $apiDebug->confirmOrderReference($basket);


        // basket setShipping/setBilling... Methode zum Setzen der Addresse
        //$createdAddress = $contactAddress->createAddress($address->toArray(),
        //   AddressRelationType::DELIVERY_ADDRESS);

        return (array)$confirmOrderResponse;
    }

    private function getLanguageCode(string $lang): string
    {
        switch ($lang) {
            case "de":
                $lang = "de-DE";
                break;
            case "en":
                $lang = "en-GB";
                break;
            case "es":
                $lang = "es-ES";
                break;
            case "fr":
                $lang = "fr-FR";
                break;
            default:
                $lang = "en-GB";
        }
        return $lang;
    }
}
