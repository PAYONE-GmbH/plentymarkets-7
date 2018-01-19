<?php

namespace Payone\Mocks;

use Plenty\Plugin\ConfigRepository;

/**
 * Class Config
 */
class Config extends ConfigRepository
{
    /**
     * @return \stdClass[]
     */
    private $config = [];

    /**
     * Config constructor.
     */
    public function __construct()
    {
        $configJson = file_get_contents((__DIR__ . DIRECTORY_SEPARATOR
            . '..' . DIRECTORY_SEPARATOR
            . '..' . DIRECTORY_SEPARATOR
            . '..' . DIRECTORY_SEPARATOR
            . '..' . DIRECTORY_SEPARATOR
            . '..' . DIRECTORY_SEPARATOR .
            'config.json'));
        $config = json_decode($configJson);
        foreach ($config as $confEntry) {
            $this->config[$confEntry->key] = $confEntry;
        }
    }

    /**
     * Getter for Config
     *
     * @return mixed
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Get the specified configuration value.
     */
    public function get(
        string $key,
        $default = null
    ) {
        $key = str_replace(self::getPrefix() . '.', '', $key);
        if (!isset($default) && !$this->has($key)) {
            throw new \InvalidArgumentException('No config for ' . $key);
        }

        return $this->getValue($key, $default);
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function has(
        string $key
    ): bool {
        return isset($this->config[$key]);
    }

    /**
     * @param string $key
     * @param null $value
     */
    public function set(
        string $key,
        $value = null
    ) {
        $this->config[$key] = $value;
    }

    /**
     * @param string $key
     * @param $value
     */
    public function prepend(
        string $key,
        $value
    ) {
        $this->config[$key] = $value;
    }

    /**
     * @param string $key
     * @param $value
     */
    public function push(
        string $key,
        $value
    ) {
        $this->config[$key] = $value;
    }

    /**
     * @return string
     */
    public static function getPrefix(): string
    {
        return 'Payone';
    }

    /**
     * @param string $key
     * @param $default
     *
     * @return mixed
     */
    private function getValue(string $key, $default)
    {
        $config = $this->config[$key];
        $configValue = null;
        if ($config->type == 'text') {
            $configValue = $config->default;
        }
        if ($config->type == 'dropdown') {
            $configValue = $config->possibleValues->{(int) $config->default};
        }

        return isset($configValue) ? $configValue : $default;
    }
}
