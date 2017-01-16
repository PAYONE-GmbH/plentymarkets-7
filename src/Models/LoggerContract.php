<?php


namespace Payone\Models;

/**
 * Interface LoggerContract
 * @package Payone\Models
 */
interface LoggerContract
{

    /**
     * @param string $message
     * @return void
     */
    public static function log($message);
}