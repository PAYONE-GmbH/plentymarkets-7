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
use Payone\Models\SepaMandateCache;
use Payone\PluginConstants;
use Payone\Services\PaymentService;
use Payone\Services\SepaMandate;
use Payone\Validator\CardExpireDate;
use Payone\Views\ErrorMessageRenderer;
use Plenty\Modules\Basket\Contracts\BasketRepositoryContract;
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
            ->debug('CheckoutController', $this->request->all());
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
            ->debug('CheckoutController', $this->request->all());
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
        $this->logger->setIdentifier(__METHOD__)->debug('Router.routeCalled', $this->request->all());

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
    public function checkoutSuccess(BasketRepositoryContract $basketReopo, PaymentHelper $helper)
    {

        $this->logger->setIdentifier(__METHOD__);
        $this->logger->debug('Controller.Success', $this->request->all());
        $basket = $basketReopo->load();
        if (!$helper->isPayonePayment($basket->methodOfPaymentId)) {
            return $this->response->redirectTo('payone/error');
        }

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
        $this->logger->debug('redirecting');

        //info would be enought but is not shown in frontend
        $notificationService->error($messageRenderer->render('Payone::Template.orderErrorMessage'));

        return $this->response->redirectTo('checkout');
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
}
