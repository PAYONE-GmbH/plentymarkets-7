<?php

//strict

namespace Payone\Services;

use Payone\Adapter\Config as ConfigAdapter;
use Payone\Helpers\PaymentHelper;
use Payone\Models\Api\ResponseAbstract;
use Payone\Models\ApiResponseCache;
use Payone\Services\Auth as AuthService;
use Plenty\Modules\Basket\Models\Basket;

/**
 * Class PaymentService
 */
class PaymentService
{
    const AUTH_TYPE_AUTH = '1';

    /**
     * @var PaymentHelper
     */
    private $paymentHelper;

    /**
     * @var ConfigAdapter
     */
    private $config;

    /**
     * @var AuthService
     */
    private $authService;
    /**
     * @var PreAuth
     */
    private $preAuthService;
    /**
     * @var ApiResponseCache
     */
    private $responseCache;

    /**
     * PaymentService constructor.
     * @param Auth $authService
     * @param PreAuth $preAuthService
     * @param ApiResponseCache $responseCache
     */
    public function __construct(
        AuthService $authService,
        PreAuth $preAuthService,
        ApiResponseCache $responseCache
    ) {
        $this->authService = $authService;
        $this->preAuthService = $preAuthService;
        $this->responseCache = $responseCache;
    }

    /**
     * @param Basket $basket
     *
     * @throws \Exception
     *
     * @return ResponseAbstract
     */
    public function openTransaction(Basket $basket): ResponseAbstract
    {
        $authType = $this->config->get('authType');
        $selectedPaymentMopId = $basket->methodOfPaymentId;
        if (!$selectedPaymentMopId || !$this->paymentHelper->isPayonePayment($selectedPaymentMopId)) {
            throw new \Exception(
                'Can no initialize payment. Not a Payone payment method'
            );
        }

        if ($authType == self::AUTH_TYPE_AUTH) {
            $executeResponse = $this->authService->executeAuth($basket);
        } else {
            $executeResponse = $this->preAuthService->executePreAuth($basket);
        }
        if (!$executeResponse->getSuccess()) {
            throw new \Exception(
                $executeResponse->getErrorMessage() ?? 'Could not initialize payment. Please choose another payment and retry'
            );
        }
        $this->responseCache->storeAuth($selectedPaymentMopId, $executeResponse);

        return $executeResponse;
    }
}
