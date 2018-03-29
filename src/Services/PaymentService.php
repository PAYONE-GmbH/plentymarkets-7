<?php

//strict

namespace Payone\Services;

use Payone\Adapter\Config as ConfigAdapter;
use Payone\Helpers\PaymentHelper;
use Payone\Models\Api\AuthResponse;
use Payone\Models\Api\PreAuthResponse;
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
     *
     * @param Auth $authService
     * @param PreAuth $preAuthService
     * @param ApiResponseCache $responseCache
     * @param ConfigAdapter $config
     * @param PaymentHelper $paymentHelper
     */
    public function __construct(
        AuthService $authService,
        PreAuth $preAuthService,
        ApiResponseCache $responseCache,
        ConfigAdapter $config,
        PaymentHelper $paymentHelper
    ) {
        $this->authService = $authService;
        $this->preAuthService = $preAuthService;
        $this->responseCache = $responseCache;
        $this->config = $config;
        $this->paymentHelper = $paymentHelper;
    }

    /**
     * @param Basket $basket
     *
     * @throws \Exception
     *
     * @return AuthResponse|PreAuthResponse
     */
    public function openTransaction(Basket $basket)
    {
        $authType = $this->config->get('authType');
        $selectedPaymentMopId = $basket->methodOfPaymentId;
        if (!$selectedPaymentMopId || !$this->paymentHelper->isPayonePayment($selectedPaymentMopId)) {
            throw new \Exception(
                'Can no initialize payment. Not a Payone payment method'
            );
        }
        if ($authType == self::AUTH_TYPE_AUTH) {
            $authResponse = $this->authService->executeAuth($basket);
        } else {
            $authResponse = $this->preAuthService->executePreAuth($basket);
        }
        if (!$authResponse->getSuccess()) {
            throw new \Exception(
                $authResponse->getErrorMessage() ?? 'Could not initialize payment. Please choose another payment and retry'
            );
        }
        $this->responseCache->storeAuth($selectedPaymentMopId, $authResponse);

        return $authResponse;
    }
}
