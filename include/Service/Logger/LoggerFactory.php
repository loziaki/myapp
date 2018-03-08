<?php
namespace Service\Logger;

use Service\Logger\LoggerInterface;

class LoggerFactory 
{
    private static $logger = [];

    public static function set($name,LoggerInterface $logger)
    {
        SELF::$logger[$name] = $logger;
    }

    public static function get($name)
    {
        return (SELF::$logger[$name])?? NULL;
    }
}