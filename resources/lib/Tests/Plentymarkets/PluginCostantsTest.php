<?php

namespace Payone\Tests\Unit;

use Payone\PluginConstants;

class PluginCostantsTest extends \PHPUnit_Framework_TestCase
{
    private $pluginConfig;

    public function setUp()
    {
        $configJson = file_get_contents(
            __DIR__ . DIRECTORY_SEPARATOR
            . '..' . DIRECTORY_SEPARATOR
            . '..' . DIRECTORY_SEPARATOR
            . '..' . DIRECTORY_SEPARATOR
            . '..' . DIRECTORY_SEPARATOR .
            'plugin.json'
        );
        $this->pluginConfig = json_decode($configJson, true);
        if (!$this->pluginConfig) {
            throw new \Exception('Plugin config can not be parsed.');
        }
    }

    public function testPluginVersionSameAsConfig()
    {
        $this->assertSame(PluginConstants::VERSION, $this->pluginConfig['version']);
    }
}
