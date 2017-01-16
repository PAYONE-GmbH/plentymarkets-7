<?php


namespace Payone\Services;

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
    public function log($message);
}