<?php

namespace Payone\Controllers;

use Payone\Helpers\PaymentHelper;
use Payone\Helpers\ShopHelper;
use Payone\Migrations\CreatePaymentMethods;
use Payone\PluginConstants;
use Payone\Providers\Api\Request\PreAuthDataProvider;
use Payone\Services\Api;
use Plenty\Modules\Authorization\Services\AuthHelper;
use Plenty\Modules\Basket\Contracts\BasketRepositoryContract;
use Plenty\Modules\Listing\ShippingProfile\Contracts\ShippingProfileRepositoryContract;
use Plenty\Modules\Payment\Method\Contracts\PaymentMethodRepositoryContract;
use Plenty\Plugin\ConfigRepository;
use Plenty\Plugin\Controller;
use Plenty\Plugin\Http\Request;

/**
 * Class ConfigController
 */
class ConfigController extends Controller
{
    /**
     * @var ConfigRepository
     */
    private $configRepo;

    /**
     * @var PaymentMethodRepositoryContract
     */
    private $paymentMethodRepo;

    /** @var PaymentHelper */
    private $paymentHelper;

    /**
     * @var ShopHelper
     */
    private $shopHelper;

    /**
     * ConfigController constructor.
     *
     * @param ConfigRepository $configRepo
     * @param PaymentMethodRepositoryContract $paymentMethodRepo
     * @param PaymentHelper $paymentHelper
     */
    public function __construct(
        ConfigRepository $configRepo,
        PaymentMethodRepositoryContract $paymentMethodRepo,
        PaymentHelper $paymentHelper,
        ShopHelper $shopHelper
    ) {
        $this->configRepo = $configRepo;
        $this->paymentMethodRepo = $paymentMethodRepo;
        $this->paymentHelper = $paymentHelper;
        $this->shopHelper = $shopHelper;
    }

    public function index()
    {
        if (!$this->shopHelper->isDebugModeActive()) {
            return;
        }
    }

    /**
     * @param Request $request
     */
    public function test(Request $request)
    {
        if (!$this->shopHelper->isDebugModeActive()) {
            return;
        }

        try {
            return json_encode($this->configRepo->get(PluginConstants::NAME), JSON_PRETTY_PRINT) .
                PHP_EOL . $request->get('configPath') . PHP_EOL .
                json_encode($this->configRepo->get($request->get('configPath')),
                    JSON_PRETTY_PRINT);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function test2(CreatePaymentMethods $migration)
    {
        if (!$this->shopHelper->isDebugModeActive()) {
            return;
        }
        try {
            $migration->run();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function test3()
    {
        if (!$this->shopHelper->isDebugModeActive()) {
            return;
        }
        $paymentMethods = $this->paymentMethodRepo->all();

        $text = '';
        foreach ($paymentMethods as $paymentMethod) {
            $text .= $paymentMethod->id . ': ' . $paymentMethod->paymentKey . PHP_EOL;
        }

        return $text;
    }

    public function test4(Request $request)
    {
        if (!$this->shopHelper->isDebugModeActive()) {
            return;
        }
    }

    /**
     * @param Request $request
     * @param Api $api
     * @param PreAuthDataProvider $provider
     * @param BasketRepositoryContract $basket
     *
     * @return string|void
     */
    public function doPreCheck(
        Request $request,
        Api $api,
        PreAuthDataProvider $provider,
        BasketRepositoryContract $basket
    ) {
        if (!$this->shopHelper->isDebugModeActive()) {
            return;
        }
        try {
            $paymentCode = $request->get('paymentCode');
            $response = $api->doPreAuth(
                $provider->getDataFromBasket($paymentCode, $basket->load())
            );

            return json_encode($response, JSON_PRETTY_PRINT);
        } catch (\Exception $e) {
            return PHP_EOL .
                $e->getCode() . PHP_EOL .
                $e->getMessage() . PHP_EOL .
                $e->getTraceAsString();
        }
    }

    /**
     * @param Request $request
     * @param PreAuthDataProvider $provider
     * @param BasketRepositoryContract $basket
     *
     * @return string|void
     */
    public function testRequestData(
        Request $request,
        PreAuthDataProvider $provider,
        BasketRepositoryContract $basket
    ) {
        if (!$this->shopHelper->isDebugModeActive()) {
            return;
        }
        try {
            return json_encode($provider->getDataFromBasket($request->get('paymentCode'), $basket->load()),
                JSON_PRETTY_PRINT);
        } catch (\Exception $e) {
            return PHP_EOL .
                $e->getCode() . PHP_EOL .
                $e->getMessage() . PHP_EOL .
                $e->getTraceAsString();
        }
    }

    /**
     * @param Request $request
     * @param ShippingProfileRepositoryContract $shippingProfileRepositoryContract
     * @return string|void
     */
    public function printShippingProfiles(
        Request $request,
        ShippingProfileRepositoryContract $shippingProfileRepositoryContract
    ) {
        if (!$this->shopHelper->isDebugModeActive()) {
            return;
        }
        try {
            $shippingProviderId = $request->get('id');

            /** @var \Plenty\Modules\Authorization\Services\AuthHelper $authHelper */
            $authHelper = pluginApp(AuthHelper::class);
            $response = $authHelper->processUnguarded(
                function () use ($shippingProfileRepositoryContract, $shippingProviderId) {
                    return $shippingProfileRepositoryContract->get($shippingProviderId);
                }
            );
            return json_encode($response, JSON_PRETTY_PRINT);
        } catch (\Exception $e) {
            return PHP_EOL .
                $e->getCode() . PHP_EOL .
                $e->getMessage() . PHP_EOL .
                $e->getTraceAsString();
        }
    }
}
