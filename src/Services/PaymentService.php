<?php

namespace Payone\Services;


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
    protected $paymentHelper;

    /**
     * @var AuthService
     */
    protected $authService;

    /**
     * @var PreAuth
     */
    protected $preAuthService;

    /**
     * @var ApiResponseCache
     */
    protected $responseCache;

    /**
     * @var SettingsService
     */
    protected $settingsService;

    /**
     * PaymentService constructor.
     *
     * @param Auth $authService
     * @param PreAuth $preAuthService
     * @param ApiResponseCache $responseCache
     * @param SettingsService $settingsService
     * @param PaymentHelper $paymentHelper
     */
    public function __construct(
        AuthService $authService,
        PreAuth $preAuthService,
        ApiResponseCache $responseCache,
        SettingsService $settingsService,
        PaymentHelper $paymentHelper
    ) {
        $this->authService = $authService;
        $this->preAuthService = $preAuthService;
        $this->responseCache = $responseCache;
        $this->settingsService = $settingsService;
        $this->paymentHelper = $paymentHelper;
    }

    /**
     * @param Basket $basket
     * @throws \Exception
     * @return AuthResponse|PreAuthResponse
     */
    public function openTransaction(Basket $basket)
    {
        $selectedPaymentMopId = $basket->methodOfPaymentId;
        if (!$selectedPaymentMopId || !$this->paymentHelper->isPayonePayment($selectedPaymentMopId)) {
            throw new \Exception(
                'Can no initialize payment. Not a Payone payment method'
            );
        }
        $authType = $this->settingsService->getPaymentSettingsValue('AuthType', $this->paymentHelper->getPaymentCodeByMop($selectedPaymentMopId));
        if(!isset($authType) || $authType == -1) {
            $authType = $this->settingsService->getSettingsValue('authType');
        }
        if ($authType == self::AUTH_TYPE_AUTH) {
            $authResponse = $this->authService->executeAuth($basket);
        } else {
            $authResponse = $this->preAuthService->executePreAuth($basket);
        }
        if (!$authResponse->getSuccess()) {
            throw new \Exception(
                $authResponse->getErrorMessage() ?? 'Could not initialize payment. Please choose another payment method and retry'
            );
        }
        $this->responseCache->storeAuth($selectedPaymentMopId, $authResponse);

        return $authResponse;
    }
}
