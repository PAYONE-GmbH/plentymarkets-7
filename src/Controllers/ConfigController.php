<?php

namespace Payone\Controllers;

use Payone\Services\MailLogger;
use Plenty\Plugin\ConfigRepository;
use Plenty\Plugin\Controller;

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
     * @param ConfigRepository $configRepo
     */
    public function __construct(ConfigRepository $configRepo)
    {
        $this->configRepo = $configRepo;
    }

    /**
     * @return void
     */
    public function index()
    {
        echo 'index';
        $config = '';

        foreach ($this->configRepo->get('Payone') as $key => $value) {
            $config .= $key . '=>' . $value . PHP_EOL;
        }
        echo $config;
    }

    /**
     * @return void
     */
    public function test()
    {
        MailLogger::log(__METHOD__ . ': calling test action');
        echo 'test';

    }
}
