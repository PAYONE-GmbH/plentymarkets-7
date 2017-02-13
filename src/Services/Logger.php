<?php


namespace Payone\Services;

use Payone\PluginConstants;
use Plenty\Modules\Plugin\Libs\Contracts\LibraryCallContract;
use Plenty\Plugin\ConfigRepository;

/**
 * Class MailLogger
 */
class Logger
{

    /**
     * @var LibraryCallContract
     */
    private $libCall;

    /**
     * @var ConfigRepository
     */
    private $config;

    /**
     * MailLogger constructor.
     * @param LibraryCallContract $libCall
     * @param $
     */
    public function __construct(LibraryCallContract $libCall, ConfigRepository $config)
    {
        $this->libCall = $libCall;
        $this->config = $config;
    }

    /**
     * @param string $message
     * @return array
     */
    public function log($message)
    {
        if (!$this->config->get(PluginConstants::NAME . '.debugging.active')) {
            return [];
        }
        $requestParams = [
            'mail' => $this->config->get(PluginConstants::NAME . '.debugging.email'),
            'subject' => 'Log message',
            'message' => $message,
            'slack' => [
                'channel' => $this->config->get(PluginConstants::NAME . '.slack.channel'),
                'user' => $this->config->get(PluginConstants::NAME . '.slack.user'),
                'token' => $this->config->get(PluginConstants::NAME . '.slack.token'),
            ]
        ];
        return $this->libCall->call('Payone::logMessage', $requestParams);

    }
}