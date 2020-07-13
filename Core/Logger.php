<?php
namespace Sailor\Core;

use Monolog\Logger as MonologLogger;
use Sailor\Core\Config;

class Logger
{
    /** @var array */
    private static $levels = [
        'DEBUG'     => \Monolog\Logger::DEBUG,
        'INFO'      => \Monolog\Logger::INFO,
        'NOTICE'    => \Monolog\Logger::NOTICE,
        'WARNING'   => \Monolog\Logger::WARNING,
        'ERROR'     => \Monolog\Logger::ERROR,
        'CRITICAL'  => \Monolog\Logger::CRITICAL,
        'ALERT'     => \Monolog\Logger::ALERT,
        'EMERGENCY' => \Monolog\Logger::EMERGENCY,
    ];

    /** @var \Monolog\Logger */
    private static $logger;

    /**
     * Initialize Logger instance of Monolog\logger
     */
    public static function initializeInstance()
    {
        if (!self::$logger instanceof \Monolog\Logger) {
            self::$logger = new \Monolog\Logger(Config::get('log.NAME'));
            self::$logger->pushProcessor(new \Monolog\Processor\PsrLogMessageProcessor);
            self::$logger->pushHandler(new \Monolog\Handler\StreamHandler(
                Config::get('log.PATH') . '/' . date('Y-m-d') . '.log',
                self::$levels[strtoupper(Config::get('log.LEVEL'))]
            ));
        }
    }

    /**
     * Log with level
     * 
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @return boolean
     */
    public static function log($level, $message, array $context=[])
    {
        self::initializeInstance();
        return self::$logger->log($level, $message, $context);
    }

    /**
     * Info message into log 
     * 
     * @param string $message
     * @param array $context
     * @return boolean
     */
    public static function info($message, array $context=[])
    {
        self::initializeInstance();
        return self::$logger->info($message, $context);
    }

    /**
     * Debug message into log
     * 
     * @param string $message
     * @param array $context
     * @return boolean
     */
    public static function debug($message, array $context=[])
    {
        self::initializeInstance();
        return self::$logger->debug($message, $context);
    }

    /**
     * Notice message into log
     * 
     * @param string $message
     * @param array $context
     * @return boolean
     */
    public static function notice($message, array $context=[])
    {
        self::initializeInstance();
        return self::$logger->notice($message, $context);
    }

    /**
     * Warn message into log
     * 
     * @param string $message
     * @param array $context
     * @return boolean
     */
    public static function warn($message, array $context=[])
    {
        self::initializeInstance();
        return self::$logger->warn($message, $context);
    }

    /**
     * Error message into log
     * 
     * @param string $message
     * @param array $context
     * @return boolean
     */
    public static function error($message, array $context=[])
    {
        self::initializeInstance();
        return self::$logger->error($message, $context);
    }

    /**
     * Critical message into to log
     * 
     * @param string $message
     * @param array $context
     * @return boolean
     */
    public static function critical($message, array $context=[])
    {
        self::initializeInstance();
        return self::$logger->critical($message, $context);
    }

    /**
     * Alert message into log
     * 
     * @param string $message
     * @param array $context
     * @return boolean 
     */
    public static function alert($message, array $context=[])
    {
        self::initializeInstance();
        return self::$logger->alert($message, $context);
    }
}