<?php

//strict

namespace Payone\Services;

use ArvPayoneApi\Response\ResponseContract;
use Payone\Adapter\Config as ConfigAdapter;
use Payone\Helpers\PaymentHelper;
use Payone\Providers\Api\Request\AuthDataProvider;
use Payone\Providers\Api\Request\PreAuthDataProvider;
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
     * @var Api
     */
    private $api;
    /**
     * @var PreAuthDataProvider
     */
    private $preAuthDataProvider;
    /**
     * @var AuthDataProvider
     */
    private $authDataProvider;

    /**
     * PaymentService constructor.
     *
     * @param PaymentMethodRepositoryContract $paymentMethodRepository
     * @param PaymentRepositoryContract $paymentRepository
     * @param ConfigAdapter $config
     * @param PaymentHelper $paymentHelper
     * @param LibraryCallContract $libCall
     * @param AddressRepositoryContract $addressRepo
     * @param PreAuthDataProvider $preAuthDataProvider
     * @param AuthDataProvider $authDataProvider
     * @param Api $api
     */
    public function __construct(
        PaymentMethodRepositoryContract $paymentMethodRepository,
        PaymentRepositoryContract $paymentRepository,
        ConfigAdapter $config,
        PaymentHelper $paymentHelper,
        LibraryCallContract $libCall,
        AddressRepositoryContract $addressRepo,
        PreAuthDataProvider $preAuthDataProvider,
        AuthDataProvider $authDataProvider,
        Api $api
    ) {
        $this->paymentMethodRepository = $paymentMethodRepository;
        $this->paymentRepository = $paymentRepository;
        $this->paymentHelper = $paymentHelper;
        $this->libCall = $libCall;
        $this->addressRepo = $addressRepo;
        $this->config = $config;
        $this->api = $api;
        $this->preAuthDataProvider = $preAuthDataProvider;
        $this->authDataProvider = $authDataProvider;
    }

    /**
     * @return array|string
     */
    public function openTransaction(Basket $basket): ResponseContract
    {
        $authType = $this->config->get('authType');
        $selectedPaymentMopId = $basket->methodOfPaymentId;
        if (!$selectedPaymentMopId || !$this->paymentHelper->isPayonePayment($selectedPaymentMopId)) {
            throw new \Exception(
                'Can no initialize payment. Not a Payone payment method'
            );
        }
        $paymentCode = $this->paymentHelper->getPaymentCodeByMop($selectedPaymentMopId);

        if ($authType == self::AUTH_TYPE_AUTH) {
            $requestData = $this->authDataProvider->getDataFromBasket($paymentCode, $basket);
            $requestData['order']['orderId'] = 'basket-' . $basket->id; //todo: transaction id
            $executeResponse = $this->api->doAuth($requestData);
        } else {
            $requestData = $this->preAuthDataProvider->getDataFromBasket($paymentCode, $basket);
            $requestData['order']['orderId'] = 'basket-' . $basket->id; //todo: transaction id
            $executeResponse = $this->api->doPreAuth($requestData);
        }
        if (!isset($executeResponse['success'])) {
            throw new \Exception(
                $executeResponse['errorMessage'] ?? 'Could not initialize payment. Please choose another payment and retry'
            );
        }

        return $executeResponse;
    }
}
