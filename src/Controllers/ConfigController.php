<?php

namespace Payone\Controllers;

use Payone\Helper\PaymentHelper;
use Payone\PluginConstants;
use Payone\Services\Logger;
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

    /** @var  PaymentHelper */
    private $paymentHelper;

    /**
     * ConfigController constructor.
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

    /**
     * @return void
     */
    public function index()
    {
        echo 'index';

        try {

            echo 'log:';
            echo json_encode($this->logger->log('test'));
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * @param Request $request
     * @return void
     */
    public function test(Request $request)
    {
        echo 'test';
        echo 'PAYONE config', PHP_EOL;

        try {

            echo json_encode($this->configRepo->get(PluginConstants::NAME . '.' )), PHP_EOL;
            echo $request->get('configPath'), PHP_EOL;
            echo json_encode($this->configRepo->get($request->get('configPath')));
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * @return void
     */
    public function test2()
    {
        echo 'test2';

    }

    /**
     * @return void
     */
    public function test3()
    {
        echo 'test3';
        $paymentMethods = $this->paymentMethodRepo->all();


        foreach ($paymentMethods as $paymentMethod) {
            echo $paymentMethod->id, ': ', $paymentMethod->paymentKey, PHP_EOL;
        }

    }

    /**
     * @return void
     */
    public function test4(Request $request)
    {
        echo 'test4';
        $paymentCode = $request->get('paymentCode');
        $config = $this->paymentHelper->getApiContextParams($paymentCode);

        echo json_encode($config);
    }


}
