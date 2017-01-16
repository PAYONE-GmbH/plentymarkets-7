<?php


namespace Payone\Models;

/**
 * Class MailLogger
 */
class MailLogger implements LoggerContract
{
    /**
     * @param string $message
     * @return void
     */
    public static function log($message)
    {
        mail('heinen@arvatis.com', 'Log message', $message);
    }
}