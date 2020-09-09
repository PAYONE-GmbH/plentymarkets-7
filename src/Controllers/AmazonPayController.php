<?php

namespace Payone\Controllers;

use Payone\PluginConstants;
use Payone\Providers\Api\Request\GenericPaymentDataProvider;
use Payone\Providers\Api\Request\Models\GenericPayment;
use Payone\Services\Api;
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
            'clientId' => "amzn1.application-oa2-client.2c027e55b128457bb16edc2f0fc6bd71",
            'sellerId' => "A13SNST9X74Q8L",
//            'clientId' => $configResponse->getClientId(),
//            'sellerId' => $configResponse->getSellerId(),
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


    public function renderWidgets(Twig $twig, Request $request)
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

        return $twig->render(PluginConstants::NAME . '::Checkout.AmazonPayWidgets', [
            'content' => $content,
            'accessToken' => $accessToken,
            'workOrderId' => $workdOrderId
        ]);
    }

    public function getOrderReference(Request $request)
    {
        $accessToken = $request->get('accessToken');
        $workOrderId = $request->get('workOrderId');
        $amazonReferenceId = $request->get('amazonReferenceId');
        $amazonAddressToken = $request->get('amazonAddressToken') ?? "";

        /** @var GenericPaymentDataProvider $genericPaymentDataProvider */
        $genericPaymentDataProvider = pluginApp(GenericPaymentDataProvider::class);
        $requestParams = $genericPaymentDataProvider->getGetOrderReferenceDetailsRequestData(
            "Amazon Pay",
            $workOrderId,
            $amazonReferenceId,
            $amazonAddressToken
        );

        $orderReferenceResponse = $this->api->doGenericPayment(GenericPayment::ACTIONTYPE_GETORDERREFERENCEDETAILS, $requestParams);


        return $orderReferenceResponse;
    }

    public function setOrderReference(Request $request)
    {
        $workOrderId = "";
        $amazonReferenceId = "";
        $amazonAddressToken = "";
        $storename = "";
        $amount = "";


        $requestParams = $this->dataProvider->getSetOrderReferenceDetailsRequestData(
            "Amazon Pay",
            $workOrderId,
            $amazonReferenceId,
            $amazonAddressToken,
            $storename,
            $amount
        );

        $configResponse = $this->api->doGenericPayment(GenericPayment::ACTIONTYPE_GETCONFIGURATION, $requestParams);

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
