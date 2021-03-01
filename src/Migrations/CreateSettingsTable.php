<?php

namespace Payone\Migrations;

use Payone\Models\Settings;
use Plenty\Modules\Plugin\DataBase\Contracts\Migrate;

/**
 * Class CreateSettingsTable
 * @package Payone\Migrations
 */
class CreateSettingsTable
{
    /**
     * @var Migrate
     */
    private $migrate;

    /**
     * CreateMethodSettingsTable constructor.
     * @param Migrate $migrate
     */
    public function __construct(Migrate $migrate)
    {
        $this->migrate = $migrate;
    }

    /**
     *
     */
    public function run()
    {
        $this->migrate->createTable(Settings::class);
    }
}