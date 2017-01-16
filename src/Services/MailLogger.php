<?php


namespace Payone\Services;

use Plenty\Modules\Plugin\Libs\Contracts\LibraryCallContract;

/**
 * Class MailLogger
 */
class MailLogger implements LoggerContract
{

    /**
     * @var LibraryCallContract
     */
    private $libCall;


    /**
     * MailLogger constructor.
     * @param LibraryCallContract $libCall
     * @param $
     */
    public function __construct(LibraryCallContract $libCall)
    {
        $this->libCall = $libCall;
    }

    /**
     * @param string $message
     * @return array
     */
    public function log($message)
    {
        $requestParams = [
            'mail' => 'heinen@arvatis.com',
            'subject' => 'Log message',
            'message' => $message
        ];
        return $this->libCall->call('Payone::logMessage', $requestParams);

    }
}