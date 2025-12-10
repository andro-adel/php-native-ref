<?php

namespace App\Services;

use Monolog\Logger as Mono;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Formatter\LineFormatter;

class Logger
{
    private static $logger = null;

    public static function get($channel = 'app')
    {
        if (self::$logger) return self::$logger;
        $log = new Mono($channel);

        // ملف يومي مع حفظ X ملفات (Rotating)
        $fileHandler = new RotatingFileHandler('/var/log/app/app.log', 7, Mono::DEBUG);
        $stdoutHandler = new StreamHandler('php://stdout', Mono::DEBUG);

        // تنسيق سطر لقراءة بسيطة
        $formatter = new LineFormatter(null, null, true, true);
        $fileHandler->setFormatter($formatter);
        $stdoutHandler->setFormatter($formatter);

        $log->pushHandler($fileHandler);
        $log->pushHandler($stdoutHandler);

        self::$logger = $log;
        return self::$logger;
    }
}
