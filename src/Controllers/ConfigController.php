<?php

namespace Payone;

use Plenty\Plugin\ConfigRepository;
use Plenty\Plugin\Controller;

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

    public function index()
    {
        echo 'index';
        $config = '';

        foreach ($this->configRepo->get('Payone') as $key => $value) {
            $config .= $key . '=>'.$value . PHP_EOL;
        }
        echo $config;
    }
}
