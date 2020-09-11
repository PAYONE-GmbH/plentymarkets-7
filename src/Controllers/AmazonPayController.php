<?php

namespace Payone\Controllers;

use Payone\Adapter\SessionStorage;
use Payone\Helpers\PaymentHelper;
use Payone\Methods\PayoneAmazonPayPaymentMethod;
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
use Plenty\Modules\Webshop\Contracts\LocalizationRepositoryContract;
use Plenty\Plugin\Controller;
use Plenty\Plugin\Http\Request;
use Plenty\Plugin\Templates\Twig;


class AmazonPayController extends Controller
{
    /** @var Api */
    private $api;

    /** @var GenericPaymentDataProvider */
    private $dataProvider;

    /**
     * AmazonPayController constructor.
     * @param Api $api
     * @param GenericPaymentDataProvider $dataProvider
     */
    public function __construct(Api $api,
                                GenericPaymentDataProvider $dataProvider)
    {
        $this->api = $api;
        $this->dataProvider = $dataProvider;
    }

    public function getAmazonPayLoginWidget(Twig $twig)
    {
        $requestParams = $this->dataProvider->getGetConfigRequestData("Amazon Pay");

        $configResponse = $this->api->doGenericPayment(GenericPayment::ACTIONTYPE_GETCONFIGURATION, $requestParams);

        /** @var LocalizationRepositoryContract $localizationRepositoryContract */
        $localizationRepositoryContract = pluginApp(LocalizationRepositoryContract::class);
        $lang = $this->getLanguageCode($localizationRepositoryContract->getLanguage());

        $content = [
//            'clientId' => "amzn1.application-oa2-client.2c027e55b128457bb16edc2f0fc6bd71",
//            'sellerId' => "A13SNST9X74Q8L",
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

        return $twig->render(PluginConstants::NAME . '::Checkout.AmazonPayLogin', ['content' => $content]);
    }


    public function renderWidgets(Twig $twig, PaymentHelper $paymentHelper, Request $request)
    {
        // AccessToken in Request
        $accessToken = $request->get('accessToken');
        $workdOrderId = $request->get('workOrderId');

        // SWAP containers here
        $content = [
            'clientId' => "amzn1.application-oa2-client.2c027e55b128457bb16edc2f0fc6bd71",
            'sellerId' => "A13SNST9X74Q8L",
            'addressBookScope' => "profile payments:widget payments:shipping_address payments:billing_address",
            'walletScope' => "profile payments:widget payments:shipping_address payments:billing_address",
        ];
        $amazonPayMopId = $paymentHelper->getMopId(PayoneAmazonPayPaymentMethod::PAYMENT_CODE);

        return $twig->render(PluginConstants::NAME . '::Checkout.AmazonPayWidgets', [
            'content' => $content,
            'accessToken' => $accessToken,
            'workOrderId' => $workdOrderId,
            'amazonPayMopId' => $amazonPayMopId
        ]);
    }

    public function getOrderReference(Request $request, Checkout $checkout)
    {
        $workOrderId = $request->get('workOrderId');
        $amazonReferenceId = $request->get('amazonReferenceId');

        /** @var SessionStorage $sessionStorage */
        $sessionStorage = pluginApp(SessionStorage::class);
        $sessionStorage->setSessionValue('workOrderId', $workOrderId);
        $sessionStorage->setSessionValue('amazonReferenceId', $amazonReferenceId);

        /** @var GenericPaymentDataProvider $genericPaymentDataProvider */
        $genericPaymentDataProvider = pluginApp(GenericPaymentDataProvider::class);
        $requestParams = $genericPaymentDataProvider->getGetOrderReferenceDetailsRequestData(
            "Amazon Pay",
            $workOrderId,
            $amazonReferenceId
        );

        /** @var GetOrderReferenceDetailsResponse $orderReferenceResponse */
        $orderReferenceResponse = $this->api->doGenericPayment(GenericPayment::ACTIONTYPE_GETORDERREFERENCEDETAILS, $requestParams);

        /** @var AmazonPayService $amazonPayService */
        $amazonPayService = pluginApp(AmazonPayService::class);
        $newAddress = $amazonPayService->registerCustomerFromAmazonPay($orderReferenceResponse);

        $checkout->setCustomerInvoiceAddressId($newAddress->id);
        $checkout->setCustomerShippingAddressId($newAddress->id);

        return json_encode($checkout, true);
    }


    public function debugTest()
    {
        /** @var GetOrderReferenceDetailsResponse $orderRefDetails */
        $orderRefDetails = pluginApp(GetOrderReferenceDetailsResponse::class);
        $orderRefDetails->shippingCompany = "TestCompany";
        $orderRefDetails->shippingFirstname = "FirstName";
        $orderRefDetails->shippingLastname = "LastName";
        $orderRefDetails->shippingStreet = "Street 123";
        $orderRefDetails->shippingZip = "12345";
        $orderRefDetails->shippingCity = "Kassel";
        $orderRefDetails->shippingCountry = "DE";

        /** @var AmazonPayService $apiDebug */
        $apiDebug = pluginApp(AmazonPayService::class);
        $address = $apiDebug->registerCustomerFromAmazonPay($orderRefDetails);

        // basket setShipping/setBilling... Methode zum Setzen der Addresse
        //$createdAddress = $contactAddress->createAddress($address->toArray(),
         //   AddressRelationType::DELIVERY_ADDRESS);

        return $address;
    }

    private function getLanguageCode(string $lang): string
    {
        switch($lang){
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
