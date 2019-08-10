<?php

namespace Sailor\Core;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Processor\PsrLogMessageProcessor;
use Symfony\Component\Console\Exception\RuntimeException;

class LoggerFactory
{
    private static $levels = [
        'DEBUG'     => Logger::DEBUG,
        'INFO'      => Logger::INFO,
        'NOTICE'    => Logger::NOTICE,
        'WARNING'   => Logger::WARNING,
        'ERROR'     => Logger::ERROR,
        'CRITICAL'  => Logger::CRITICAL,
        'ALERT'     => Logger::ALERT,
        'EMERGENCY' => Logger::EMERGENCY,
    ];
    private static $loggers = [];
    private static $currentChannel; 
    public static function create($name, $path)
    {
        if (!is_string($name)) {
            throw new RuntimeException('The Logger Name must be String!');
        }

        if (!is_dir($path)) {
            throw new RuntimeException('The log directory does not exist!');
        }

        $logger = new Logger($name);
        $logger->pushProcessor(new PsrLogMessageProcessor());
        $logger->pushHandler(new StreamHandler(
            $path . '/' . date('Y-m-d') . '.log',
            self::level(Config::get('project.LOG_LEVEL'))));
        self::$loggers[$name] = $logger;
    }

    public static function select($name) 
    {
        if (!is_string($name)) {
            return null;
        }

        if (!isset(self::$loggers[$name])) {
            return null;
        }

        self::$currentChannel = $name;
    }

    public static function getLogger($channel=NULL)
    {
        if (is_null($channel)) {
            return self::$loggers[self::$currentChannel];
        }
        return self::$loggers[$channel];
    }

    public static function info($message, array $context = [])
    {
        if (empty(self::$currentChannel)) {
            return false;
        }
        
        self::$loggers[self::$currentChannel]->info($message, $context);
    }

    public static function notice($message, array $context = [])
    {
        if (empty(self::$currentChannel)) {
            return false;
        }
        
        self::$loggers[self::$currentChannel]->notice($message, $context);
    }

    public static function warn($message, array $context = [])
    {
        if (empty(self::$currentChannel)) {
            return false;
        }
        
        self::$loggers[self::$currentChannel]->warn($message, $context);
    }

    public static function error($message, array $context = [])
    {
        if (empty(self::$currentChannel)) {
            return false;
        }
        
        self::$loggers[self::$currentChannel]->error($message, $context);
    }

    public static function critical($message, array $context = [])
    {
        if (empty(self::$currentChannel)) {
            return false;
        }
        
        self::$loggers[self::$currentChannel]->critical($message, $context);
    }

    public static function alert($message, array $context = [])
    {
        if (empty(self::$currentChannel)) {
            return false;
        }
        
        self::$loggers[self::$currentChannel]->alert($message, $context);
    }

    public static function emrgency($message, array $context = [])
    {
        if (empty(self::$currentChannel)) {
            return false;
        }
        
        self::$loggers[self::$currentChannel]->emrgency($message, $context);
    }

    private static function level($level)
    {
        if (!is_string($level)) {
            return false;
        }

        if (isset(self::$levels[strtoupper($level)])) {
            return self::$levels[strtoupper($level)];
        }
        return false;
    }
}