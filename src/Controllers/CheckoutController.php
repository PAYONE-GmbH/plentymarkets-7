<?php

namespace Payone\Controllers;

use IO\Services\NotificationService;
use Payone\Adapter\Logger;
use Payone\Helpers\PaymentHelper;
use Payone\Helpers\SessionHelper;
use Payone\Helpers\ShopHelper;
use Payone\Models\BankAccount;
use Payone\Models\BankAccountCache;
use Payone\Models\CreditCardCheckResponse;
use Payone\Models\CreditCardCheckResponseRepository;
use Payone\Models\PaymentCache;
use Payone\Models\SepaMandateCache;
use Payone\PluginConstants;
use Payone\Providers\Api\Request\GenericPaymentDataProvider;
use Payone\Providers\Api\Request\Models\GenericPayment;
use Payone\Services\Api;
use Payone\Services\PaymentService;
use Payone\Services\SepaMandate;
use Payone\Validator\CardExpireDate;
use Payone\Views\ErrorMessageRenderer;
use PayoneApi\Request\PaymentTypes;
use Plenty\Modules\Basket\Contracts\BasketRepositoryContract;
use Plenty\Modules\Webshop\Contracts\LocalizationRepositoryContract;
use Plenty\Plugin\Controller;
use Plenty\Plugin\Http\Request;
use Plenty\Plugin\Http\Response;
use Plenty\Plugin\Templates\Twig;

/**
 * Class CheckoutController
 */
class CheckoutController extends Controller
{
    /** @var SessionHelper */
    private $sessionHelper;

    /** @var ErrorMessageRenderer */
    private $renderer;
    /**
     * @var Request
     */
    private $request;

    /**
     * @var Logger
     */
    private $logger;
    /**
     * @var Response
     */
    private $response;

    /**
     * CheckoutController constructor.
     *
     * @param SessionHelper $sessionHelper
     * @param ErrorMessageRenderer $renderer
     * @param Request $request
     * @param Logger $logger
     * @param Response $response
     */
    public function __construct(
        SessionHelper $sessionHelper,
        ErrorMessageRenderer $renderer,
        Request $request,
        Logger $logger,
        Response $response
    ) {
        $this->sessionHelper = $sessionHelper;
        $this->renderer = $renderer;
        $this->request = $request;
        $this->logger = $logger;
        $this->response = $response;
    }

    /**
     * @param PaymentService $paymentService
     * @param BasketRepositoryContract $basket
     *
     * @return string
     */
    public function doAuth(
        PaymentService $paymentService,
        BasketRepositoryContract $basket
    ) {
        $this->logger->setIdentifier(__METHOD__)
            ->debug('Controller.Checkout', $this->request->all());
        if (!$this->sessionHelper->isLoggedIn()) {
            return $this->getJsonErrors([
                'message' => 'Your session expired. Please login and start a new purchase.',
            ]);
        }
        try {
            $auth = $paymentService->openTransaction($basket->load());
        } catch (\Exception $e) {
            return $this->getJsonErrors(['message' => $e->getMessage()]);
        }

        return $this->getJsonSuccess($auth);
    }

    /**
     * @param CreditCardCheckResponseRepository $repository
     * @param CreditCardCheckResponse $response
     * @param CardExpireDate $validator
     *
     * @return string
     */
    public function storeCCCheckResponse(
        CreditCardCheckResponseRepository $repository,
        CreditCardCheckResponse $response,
        CardExpireDate $validator
    ) {
        $this->logger->setIdentifier(__METHOD__)
            ->debug('Controller.Checkout', $this->request->all());
        if (!$this->sessionHelper->isLoggedIn()) {
            return $this->getJsonErrors(['message' => 'Your session expired. Please login and start a new purchase.']);
        }
        $status = $this->request->get('status');
        if ($status !== 'VALID') {
            return $this->getJsonErrors(['message' => 'Credit card check failed.']);
        }
        try {
            $response->init(
                $this->request->get('status'),
                $this->request->get('pseudocardpan'),
                $this->request->get('truncatedcardpan'),
                $this->request->get('cardtype'),
                $this->request->get('cardexpiredate')
            );
            $validator->validate(\DateTime::createFromFormat('Y-m-d', $response->getCardexpiredate()));
            $repository->storeLastResponse($response);
        } catch (\Exception $e) {
            return $this->getJsonErrors(['message' => $e->getMessage()]);
        }

        return $this->getJsonSuccess($response);
    }

    /**
     * @param BankAccount $bankAccount
     * @param BankAccountCache $accountCache
     * @param SepaMandate $mandateService
     * @param SepaMandateCache $mandateCache
     * @param BasketRepositoryContract $basket
     *
     * @return string
     */
    public function storeAccountData(
        BankAccount $bankAccount,
        BankAccountCache $accountCache,
        SepaMandate $mandateService,
        SepaMandateCache $mandateCache,
        BasketRepositoryContract $basket
    ) {
        $errors = [];

        if (!$this->sessionHelper->isLoggedIn()) {
            return $this->getJsonErrors([
                'message' => $this->renderer->render(
                    'Your session expired. Please login and start a new purchase.'
                ),
            ]);
        }

        $formData = [
            'holder' => $this->request->get('holder'),
            'iban' => $this->request->get('iban'),
            'bic' => $this->request->get('bic'),
        ];
        $this->logger->setIdentifier(__METHOD__)->debug('Controller.routeCalled', $this->request->all());

        foreach ($formData as $key => $value) {
            if (empty($formData[$key])) {
                $errors[$key] = true;
            }
        }

        if ($errors) {
            return $this->getJsonErrors($errors);
        }

        $accountCache->storeBankAccount(
            $bankAccount->init(
                $this->request->get('holder'),
                $this->request->get('iban'),
                $this->request->get('bic')
            )
        );

        try {
            $mandate = $mandateService->createMandate($basket->load());
        } catch (\Exception $e) {
            return $this->getJsonErrors([
                'message' => $this->renderer->render(
                    $e->getMessage()
                ),
            ]);
        }
        $sepaMandate = $mandate->getMandate();

        $mandateCache->store($sepaMandate);

        return $this->getJsonSuccess($sepaMandate);
    }

    /**
     * @param Twig $twig
     * @param SepaMandateCache $sepaMandateCache
     * @param ShopHelper $helper
     *
     * @return string
     */
    public function getSepaMandateStep(Twig $twig, SepaMandateCache $sepaMandateCache, ShopHelper $helper)
    {
        if (!$this->sessionHelper->isLoggedIn()) {
            return $this->getJsonErrors([
                'message' => $this->renderer->render(
                    'Your session expired. Please login and start a new purchase.'
                ),
            ]);
        }

        try {
            $mandate = $sepaMandateCache->load();
            $html = $twig->render(PluginConstants::NAME . '::Partials.PaymentForm.PAYONE_PAYONE_DIRECT_DEBIT_MANDATE', [
                'mandate' => $mandate,
                'locale' => $helper->getCurrentLocale(),
            ]);
        } catch (\Exception $e) {
            return $this->getJsonErrors([
                'message' => $this->renderer->render(
                    $e->getMessage()
                ),
            ]);
        }

        return $this->getJsonSuccess(
            [
                'html' => $html,
            ]
        );
    }

    /**
     * @param BasketRepositoryContract $basketReopo
     * @param PaymentHelper $helper
     *
     * @return string
     */
    public function checkoutSuccess(BasketRepositoryContract $basketReopo, PaymentHelper $helper, PaymentCache $paymentCache)
    {

        $this->logger->setIdentifier(__METHOD__);
        $this->logger->debug('Controller.Success', $this->request->all());
        $transactionBasketId = $this->request->get('transactionBasketId');
        if(strlen($transactionBasketId)){
            $storedBasketId = $paymentCache->getActiveBasketId();
            if($storedBasketId === null){
                return $this->response->redirectTo('confirmation');
            }
            if($storedBasketId != $transactionBasketId){
                return $this->response->redirectTo('payone/error');
            }
        }
        else{
            return $this->response->redirectTo('payone/error');
        }
        $basket = $basketReopo->load();
        if (!$helper->isPayonePayment($basket->methodOfPaymentId)) {
            return $this->response->redirectTo('payone/error');
        }

        $paymentCache->resetActiveBasketId();
        return $this->response->redirectTo('place-order');
    }

    /**
     * @param NotificationService $notificationService
     * @param ErrorMessageRenderer $messageRenderer
     *
     * @return string
     */
    public function redirectWithNotice(
        NotificationService $notificationService,
        ErrorMessageRenderer $messageRenderer
    ) {
        $this->logger->setIdentifier(__METHOD__);
        $this->logger->debug('Controller.redirecting');

        //info would be enought but is not shown in frontend
        $notificationService->error($messageRenderer->render('Payone::Template.orderErrorMessage'));

        return $this->response->redirectTo('checkout');
    }

    public function getAmazonPayLoginWidget(Twig $twig)
    {
        /** @var Api $api */
        $api = pluginApp(Api::class);

        /** @var GenericPaymentDataProvider $genericPaymentDataProvider */
        $genericPaymentDataProvider = pluginApp(GenericPaymentDataProvider::class);
        $requestParams = $genericPaymentDataProvider->getGetConfigRequestData("Amazon Pay");

        $configResponse = $api->doGenericPayment(GenericPayment::ACTIONTYPE_GETCONFIGURATION, $requestParams);

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
            'redirectUrl' => "https://pm-order.plentymarkets-cloud01.com/checkout",
            'debug1' => $configResponse->getWorkorderId()
        ];

        return $twig->render(PluginConstants::NAME . '::Checkout.AmazonPayLogin', ['content' => $content]);
    }

    public function swapAmazonPayWidgets()
    {
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



        // SWAP containers here
        $content = [
            'clientId' => 1,
            'sellerId' => 2,
            'addressBookScope' => 3,
            'walletScope' => 3,
        ];
    }

    /**
     * @param null $data
     *
     * @return string
     */
    private function getJsonSuccess($data = null)
    {
        return $this->response->json(['success' => true, 'message' => null, 'data' => $data]);
    }

    /**
     * @param $errors
     *
     * @return string
     */
    private function getJsonErrors($errors)
    {
        $data = [];
        $data['success'] = false;
        $data['errors'] = $errors;

        return $this->response->json($data, Response::HTTP_BAD_REQUEST);
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
