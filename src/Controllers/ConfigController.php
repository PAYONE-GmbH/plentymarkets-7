<?php

namespace Payone\Controllers;

use Payone\Helpers\PaymentHelper;
use Payone\Helpers\ShopHelper;
use Payone\PluginConstants;
use Payone\Providers\ApiRequestDataProvider;
use Payone\Services\Api;
use Plenty\Modules\Basket\Contracts\BasketRepositoryContract;
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
        echo 'test';
        echo 'PAYONE config', PHP_EOL;

        try {
            echo json_encode($this->configRepo->get(PluginConstants::NAME), JSON_PRETTY_PRINT), PHP_EOL;
            echo $request->get('configPath'), PHP_EOL;
            echo json_encode($this->configRepo->get($request->get('configPath')), JSON_PRETTY_PRINT);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function test2()
    {
        if (!$this->shopHelper->isDebugModeActive()) {
            return;
        }
        echo 'test2';
    }

    public function test3()
    {
        if (!$this->shopHelper->isDebugModeActive()) {
            return;
        }
        echo 'test3';
        $paymentMethods = $this->paymentMethodRepo->all();

        foreach ($paymentMethods as $paymentMethod) {
            echo $paymentMethod->id, ': ', $paymentMethod->paymentKey, PHP_EOL;
        }
    }

    public function test4(Request $request)
    {
        if (!$this->shopHelper->isDebugModeActive()) {
            return;
        }
        echo 'test4';
        $paymentCode = $request->get('paymentCode');
        $config = $this->paymentHelper->getApiContextParams($paymentCode);

        echo json_encode($config, JSON_PRETTY_PRINT);
    }

    /**
     * @param Request $request
     * @param Api $api
     * @param ApiRequestDataProvider $provider
     * @param BasketRepositoryContract $basket
     */
    public function doPreCheck(
        Request $request,
        Api $api,
        ApiRequestDataProvider $provider,
        BasketRepositoryContract $basket
    ) {
        if (!$this->shopHelper->isDebugModeActive()) {
            return;
        }
        try {
            $paymentCode = $request->get('paymentCode');
            $response = $api->doPreCheck(
                $paymentCode,
                $provider->getPreAuthData($paymentCode, $basket->load())
            );
            echo json_encode($response, JSON_PRETTY_PRINT);
        } catch (\Exception $e) {
            echo PHP_EOL,
            $e->getCode(), PHP_EOL,
            $e->getMessage(), PHP_EOL,
            $e->getTraceAsString();
        }
    }

    /**
     * @param Request $request
     * @param ApiRequestDataProvider $provider
     * @param BasketRepositoryContract $basket
     */
    public function testRequestData(
        Request $request,
        ApiRequestDataProvider $provider,
        BasketRepositoryContract $basket
    ) {
        if (!$this->shopHelper->isDebugModeActive()) {
            return;
        }
        try {
            echo json_encode($provider->getPreAuthData($request->get('paymentCode'), $basket->load()),
                JSON_PRETTY_PRINT);
        } catch (\Exception $e) {
            echo PHP_EOL,
            $e->getCode(), PHP_EOL,
            $e->getMessage(), PHP_EOL,
            $e->getTraceAsString();
        }
    }
}
