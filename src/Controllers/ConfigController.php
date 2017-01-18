<?php

namespace Payone\Controllers;

use League\Flysystem\Exception;
use Payone\Services\MailLogger;
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
     * ConfigController constructor.
     * @param MailLogger $logger
     */
    public function __construct(MailLogger $logger, ConfigRepository $configRepo)
    {
        $this->logger = $logger;
        $this->configRepo = $configRepo;
    }

    /**
     * @return void
     */
    public function index()
    {
        echo 'index';
        echo 'config:';
        try {
            $config = '';

            foreach ($this->configRepo->get('Payone') as $key => $value) {
                $config .= $key . '=>' . $value . PHP_EOL;
            }
            echo $config;

            echo 'log:';
            $this->logger->log('test');
        }catch (Exception $e){
            echo $e->getMessage();
        }
    }

    /**
     * @return void
     */
    public function test()
    {
        echo 'test';
        try {
            echo 'disabled php functions', PHP_EOL, ini_get('disable_functions');
            $config = '';

            foreach ($this->configRepo->get('Payone') as $key => $value) {
                $config .= $key . '=>' . $value . PHP_EOL;
            }
            echo $config;
        }catch (Exception $e){
            echo $e->getMessage();
        }
    }

    /**
     * @return void
     */
    public function test2()
    {
        echo 'test';
        echo 'disabled php functions', PHP_EOL, ini_get('disable_functions');
        $config = '';

        foreach ($this->configRepo->get('Payone') as $key => $value) {
            $config .= $key . '=>' . $value . PHP_EOL;
        }
        echo $config;
    }


}
