<?php

namespace Payone\Controllers;

use Payone\Adapter\Logger;
use Payone\Adapter\SessionStorage;
use Payone\Helpers\PaymentHelper;
use Payone\Helpers\SessionHelper;
use Payone\Services\PaymentService;
use Payone\Views\CheckoutErrorRenderer;
use Plenty\Modules\Basket\Contracts\BasketRepositoryContract;
use Plenty\Plugin\Controller;

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
     * CheckoutController constructor.
     *
     * @param Logger $logger
     * @param SessionStorage $sessionStorage
     * @param PaymentHelper $paymentHelper
     * @param SessionHelper $sessionHelper
     * @param CheckoutErrorRenderer $renderer
     */
    public function __construct(
        SessionHelper $sessionHelper,
        CheckoutErrorRenderer $renderer
    ) {
        $this->sessionHelper = $sessionHelper;
        $this->renderer = $renderer;
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
        if (!$this->sessionHelper->isLoggedIn()) {
            return $this->getJsonErrors([
                'message' => $this->renderer->renderErrorMessage(
                    'Your session expired. Please login and start a new purchase.'
                ),
            ]);
        }
        try {
            $paymentService->openTransaction($basket->load());
        } catch (\Exception $e) {
            return $this->getJsonErrors([
                'message' => $this->renderer->renderErrorMessage(
                    $e->getCode() . PHP_EOL . $e->getMessage() . PHP_EOL . $e->getTraceAsString()
                ),
            ]);
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
