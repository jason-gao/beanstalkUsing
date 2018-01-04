<?php

namespace beanstalkUsing;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class Log
{
    private static $logger;

    public static function getInstance($name = 'test', $file = '/tmp/testLog.log')
    {
        if (is_null(self::$logger) || !is_object(self::$logger)) {
            self::$logger = new Logger($name);
            $stream       = new StreamHandler($file, Logger::DEBUG);
            self::$logger->pushHandler($stream);
        }

        return self::$logger;
    }
}