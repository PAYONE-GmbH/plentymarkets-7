<?php

namespace Payone\Controllers;

use Payone\Services\MailLogger;
use Plenty\Modules\Payment\Method\Contracts\PaymentMethodRepositoryContract;
use Plenty\Plugin\ConfigRepository;
use Plenty\Plugin\Controller;

/**
 * Class ConfigController
 */
class ConfigController extends Controller
{
    /**
     * @var MailLogger
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

    /**
     * ConfigController constructor.
     * @param MailLogger $logger
     */
    public function __construct(
        MailLogger $logger,
        ConfigRepository $configRepo,
        PaymentMethodRepositoryContract $paymentMethodRepo
    ) {
        $this->logger = $logger;
        $this->configRepo = $configRepo;
        $this->paymentMethodRepo = $paymentMethodRepo;
    }

    /**
     * @return void
     */
    public function index()
    {
        echo 'index';
/*
        try {

            echo 'log:';
            $this->logger->log('test');
        } catch (\Exception $e) {
            echo $e->getMessage();
        }*/
    }

    /**
     * @return void
     */
    public function test()
    {
        echo 'test';
        /*try {

            $config = '';
            foreach ($this->configRepo->get('Payone') as $key => $value) {
                $config .= $key . '=>' . $value . PHP_EOL;
            }
            echo $config;
        } catch (\Exception $e) {
            echo $e->getMessage();
        }*/
    }

    /**
     * @return void
     */
    public function test2()
    {
        echo 'test2';
       /* echo 'disabled php functions', PHP_EOL, ini_get('disable_functions');*/

    }

    /**
     * @return void
     */
    public function test3()
    {
        echo 'test3';
        $paymentMethods = $this->paymentMethodRepo->all();


        foreach ($paymentMethods as $paymentMethod) {
            echo $paymentMethod->paymentKey;
        }

    }


}
