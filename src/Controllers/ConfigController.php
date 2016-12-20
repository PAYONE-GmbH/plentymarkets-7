<?php

namespace Payone;

use Plenty\Plugin\Controller;
use Plenty\Plugin\Templates\Twig;

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

    public function index(): string
    {
        return print_r(get_object_vars($this->configRepo), true) . PHP_EOL .
            print_r($this->configRepo->get('Payone'), true);
    }
}
