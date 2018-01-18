<?php

namespace Payone\Controllers;

use Payone\Adapter\Logger;
use Payone\Helpers\SessionHelper;
use Payone\Models\CreditCardCheckResponse;
use Payone\Models\CreditCardCheckResponseRepository;
use Payone\Services\PaymentService;
use Payone\Views\CheckoutErrorRenderer;
use Plenty\Modules\Basket\Contracts\BasketRepositoryContract;
use Plenty\Plugin\Controller;
use Plenty\Plugin\Http\Request;

/**
 * Class CheckoutController
 */
class CheckoutController extends Controller
{
    /** @var SessionHelper */
    private $sessionHelper;

    /** @var CheckoutErrorRenderer */
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
     * CheckoutController constructor.
     *
     * @param SessionHelper $sessionHelper
     * @param CheckoutErrorRenderer $renderer
     * @param Request $request
     * @param Logger $logger
     */
    public function __construct(
        SessionHelper $sessionHelper,
        CheckoutErrorRenderer $renderer,
        Request $request,
        Logger $logger
    ) {
        $this->sessionHelper = $sessionHelper;
        $this->renderer = $renderer;
        $this->request = $request;
        $this->logger = $logger;
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
                'message' => 'Your session expired. Please login and start a new purchase.'
            ]);
        }
        try {
            $paymentService->openTransaction($basket->load());
        } catch (\Exception $e) {
            return $this->getJsonErrors(['message' => $e->getCode() . PHP_EOL . $e->getMessage() . PHP_EOL . $e->getTraceAsString()]);
        }

        return $this->getJsonSuccess();
    }

    /**
     * @param CreditCardCheckResponseRepository $repository
     * @param CreditCardCheckResponse $response
     *
     * @return string
     */
    public function storeCCCheckResponse(
        CreditCardCheckResponseRepository $repository,
        CreditCardCheckResponse $response
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
            $repository->storeLastResponse($response);
        } catch (\Exception $e) {
            return $this->getJsonErrors(['message' => $e->getCode() . PHP_EOL . $e->getMessage() . PHP_EOL . $e->getTraceAsString()]);
        }

        return $this->getJsonSuccess();
    }

    /**
     * @return string
     */
    private function getJsonSuccess(): string
    {
        return json_encode(['success' => true]);
    }

    /**
     * @param $errors
     *
     * @return string
     */
    private function getJsonErrors($errors): string
    {
        $data = [];
        $data['success'] = false;
        $data['errors'] = $errors;

        return json_encode($data);
    }
}
