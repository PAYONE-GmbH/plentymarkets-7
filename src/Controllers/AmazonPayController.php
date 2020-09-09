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

    public function getOrderReference()
    {
        $accestoken = "Atza|IwEBIJZWMleom3psDFOhELQjK6lHD-XtaxDJJhI4z7TzpELpshPpyJRSq-Zt3a5yPW7EjwWczlrBF2Vj6TgoRE4HPoGRiYhor5aqphG8iFKj-ATAFKHDspzQXl68xl0nozJSjUXNtdoK_LO-X7P0KZnw8Q2f6uojm1R1MkxGwLjgn96Y5gwE1eJ1_YJVxv-zpQahxJagDyGIlEWbX2AqtEArP_l8cR6n58hxDh_1olffwjk4XxlpVFlBNaI6lnJX15EamZkojPBkNRp3NGBMzJDGlXOapRtTCq5O56LZmVJaH8r2fWzaLYqyWl2cuRI7N6ioFoG-TVr4zQvxvgJzro8vn-jhSvPKq0k-0gusOG-iM6tWPwIxZ12eeljOkKJU8VV_nHS1KhKJRCvucb_X_ulWUoqxhrdmhvr4uRuuDszX7inVZQ";
        $workorderId = "123";
        $amazonReferenceId = "123";
        $amazonAddressToken = "123";

        /** @var Api $api */
        $api = pluginApp(Api::class);

        /** @var GenericPaymentDataProvider $genericPaymentDataProvider */
        $genericPaymentDataProvider = pluginApp(GenericPaymentDataProvider::class);
        $requestParams = $genericPaymentDataProvider->getGetOrderReferenceDetailsRequestData(
            "Amazon Pay",
            $workorderId,
            $amazonReferenceId,
            $amazonAddressToken
        );
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


}
