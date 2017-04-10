<?php
declare(strict_types=1);

namespace Payone\Controllers;

use Payone\Helper\PaymentHelper;
use Payone\PluginConstants;
use Payone\Providers\ApiRequestDataProvider;
use Payone\Services\Api;
use Payone\Services\Logger;
use Plenty\Modules\Basket\Contracts\BasketRepositoryContract;
use Plenty\Modules\Payment\Method\Contracts\PaymentMethodRepositoryContract;
use Plenty\Modules\Plugin\Libs\Contracts\LibraryCallContract;
use Plenty\Plugin\ConfigRepository;
use Plenty\Plugin\Controller;
use Plenty\Plugin\Http\Request;

/**
 * Class ConfigController
 */
class ConfigController extends Controller
{
    /**
     * @var Logger
     */
    private $logger;

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
     * ConfigController constructor.
     *
     * @param Logger $logger
     * @param ConfigRepository $configRepo
     * @param PaymentMethodRepositoryContract $paymentMethodRepo
     * @param PaymentHelper $paymentHelper
     */
    public function __construct(
        Logger $logger,
        ConfigRepository $configRepo,
        PaymentMethodRepositoryContract $paymentMethodRepo,
        PaymentHelper $paymentHelper
    ) {
        $this->logger = $logger;
        $this->configRepo = $configRepo;
        $this->paymentMethodRepo = $paymentMethodRepo;
        $this->paymentHelper = $paymentHelper;
    }

    public function index()
    {
        echo 'index';

        try {
            echo 'log:';
            echo json_encode($this->logger->log('test'), JSON_PRETTY_PRINT);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * @param Request $request
     */
    public function test(Request $request)
    {
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
        echo 'test2';
    }

    public function test3()
    {
        echo 'test3';
        $paymentMethods = $this->paymentMethodRepo->all();

        foreach ($paymentMethods as $paymentMethod) {
            echo $paymentMethod->id, ': ', $paymentMethod->paymentKey, PHP_EOL;
        }
    }

    public function test4(Request $request)
    {
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
     * @param LibraryCallContract $libCall
     * @param Request $request
     * @return mixed
     */
    public function testSdkApi(
        LibraryCallContract $libCall,
        Request $request
    ) {
        try {

            echo json_encode($libCall->call(
                PluginConstants::NAME . '::guzzle_connector',
                ['packagist_query' => $request->get('search')]
            ), JSON_PRETTY_PRINT);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}
