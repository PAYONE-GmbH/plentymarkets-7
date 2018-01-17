<?php

namespace Payone\Adapter;

use Payone\PluginConstants;
use Plenty\Plugin\ConfigRepository;

/**
 * Class Config
 */
class Config //extends ConfigRepository
{
    const MULTI_SELECT_ALL = 'all';

    /**
     * @var ConfigRepository
     */
    private $config;
    /**
     * @var Logger
     */
    private $logger;

    /**
     * Config constructor.
     *
     * @param ConfigRepository $config
     * @param Logger $logger
     */
    public function __construct(ConfigRepository $config, Logger $logger)
    {
        $this->config = $config;
        $this->logger = $logger;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function has(
        string $key
    ): bool {
        $this->logger->setIdentifier(__METHOD__)->debug('Config.has', ['key' => $key]);

        return $this->config->has(PluginConstants::NAME . '.' . $key);
    }

    /**
     * @param string $key
     * @param null $default
     *
     * @return string
     */
    public function get(
        string $key,
        $default = null
    ) {
        $value = $this->config->get(PluginConstants::NAME . '.' . $key);
        $this->logger->setIdentifier(__METHOD__)->debug(
            'Config.get( ' . $key . ', ' . $default . ' )',
            ['key' => $key, 'default' => $default, 'value' => $value]
        );

        return $value;
    }

    /**
     * @param string $key
     * @param null $value
     */
    public function set(
        string $key,
        $value = null
    ) {
        $this->logger->setIdentifier(__METHOD__)->debug('Config.set', ['key' => $key, 'value' => $value]);
        $this->config->set(PluginConstants::NAME . '.' . $key, $value);
    }

    /**
     * @param string $key
     * @param $value
     */
    public function prepend(
        string $key,
        $value
    ) {
        $this->logger->setIdentifier(__METHOD__)->debug('Config.prepend', ['key' => $key, 'value' => $value]);
        $this->config->prepend(PluginConstants::NAME . $key, $value);
    }

    /**
     * @param string $key
     * @param $value
     */
    public function push(
        string $key,
        $value
    ) {
        $this->logger->setIdentifier(__METHOD__)->debug('Config.push', ['key' => $key, 'value' => $value]);
        $this->config->push(PluginConstants::NAME . $key, $value);
    }

    /**
     * @throws \Exception
     *
     * @return string
     */
    public static function getPrefix(): string
    {
        $logger = pluginApp(Logger::class);
        $logger->setIdentifier(__METHOD__)->debug('Config.getPrefix', []);
        throw new \Exception('not implemented');
        return '';
    }
}
