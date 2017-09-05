<?php

//strict

namespace Payone\Services;

use Payone\Adapter\Config as ConfigAdapter;
use Payone\Helpers\PaymentHelper;
use Payone\Models\Api\ResponseAbstract;
use Payone\Services\Auth as AuthService;
use Plenty\Modules\Account\Address\Contracts\AddressRepositoryContract;
use Plenty\Modules\Basket\Models\Basket;
use Plenty\Modules\Payment\Contracts\PaymentRepositoryContract;
use Plenty\Modules\Payment\Method\Contracts\PaymentMethodRepositoryContract;
use Plenty\Modules\Plugin\Libs\Contracts\LibraryCallContract;

/**
 * Class PaymentService
 */
class PaymentService
{
    const AUTH_TYPE_AUTH = '1';

    /**
     * @var PaymentMethodRepositoryContract
     */
    private $paymentMethodRepository;

    /**
     * @var PaymentRepositoryContract
     */
    private $paymentRepository;

    /**
     * @var PaymentHelper
     */
    private $paymentHelper;

    /**
     * @var LibraryCallContract
     */
    private $libCall;

    /**
     * @var AddressRepositoryContract
     */
    private $addressRepo;

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
     * PaymentService constructor.
     *
     * @param PaymentMethodRepositoryContract $paymentMethodRepository
     * @param PaymentRepositoryContract $paymentRepository
     * @param ConfigAdapter $config
     * @param PaymentHelper $paymentHelper
     * @param LibraryCallContract $libCall
     * @param AddressRepositoryContract $addressRepo
     * @param Auth $authService
     * @param PreAuth $preAuthService
     */
    public function __construct(
        PaymentMethodRepositoryContract $paymentMethodRepository,
        PaymentRepositoryContract $paymentRepository,
        ConfigAdapter $config,
        PaymentHelper $paymentHelper,
        LibraryCallContract $libCall,
        AddressRepositoryContract $addressRepo,
        AuthService $authService,
        PreAuth $preAuthService
    ) {
        $this->paymentMethodRepository = $paymentMethodRepository;
        $this->paymentRepository = $paymentRepository;
        $this->paymentHelper = $paymentHelper;
        $this->libCall = $libCall;
        $this->addressRepo = $addressRepo;
        $this->config = $config;
        $this->authService = $authService;
        $this->preAuthService = $preAuthService;
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

        return $executeResponse;
    }
}
