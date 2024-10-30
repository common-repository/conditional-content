<?php
/**
 * Conditional Content is a solution which helps you to insert conditional content
 * on the WordPress pages/posts/sidebars
 *
 * Copyright (c) 2017 EXTREME IDEA LLC. All Rights Reserved.
 * This software is the proprietary information of EXTREME IDEA LLC.
 *
 * Author URI: http://www.extreme-idea.com/
 * License: GPL version 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */
namespace com\extremeidea\wordpress\plugin\conditional\content\classes\core;

use com\extremeidea\php\tools\log4php as log4php;

/**
 * Class Logger
 *
 * @package com\extremeidea\wordpress\plugin\conditional\content\classes\core
 */
class Logger {

    /**
     * Settings
     *
     * @var Settings $settings object with settings
     */
    private $settings;

    /**
     * Prepare logger
     *
     * @var bool $configured flag
     */
    private static $configured = false;

    /**
     * Logger constructor.
     *
     * @param Settings $settings project settings
     *
     * @return void
     */
    public function __construct(Settings $settings) {
        $this->settings = $settings;

        if (self::$configured === false) {
            self::$configured = true;
            log4php\Logger::configure(dirname(__FILE__) . '/log4php.xml');
        }
    }

    /**
     * Get logger instance
     * 
     * @param string $name logger name
     *
     * @return mixed
     */
    public function getLogger($name) {
        static $loggers;

        if (!$loggers) {
            $loggers = array();
        }

        if (!isset($loggers[$name])) {
            $loggers[$name] = log4php\Logger::getLogger($name);
        }

        $logger = &$loggers[$name];
        $level = $this->settings->getLoggingLevel();

        switch ($level) {
            case 'all':
                $logger->setLevel(log4php\LoggerLevel::getLevelAll());
                break;
            case 'fatal':
                $logger->setLevel(log4php\LoggerLevel::getLevelFatal());
                break;
            case 'error':
                $logger->setLevel(log4php\LoggerLevel::getLevelError());
                break;
            case 'warn':
                $logger->setLevel(log4php\LoggerLevel::getLevelWarn());
                break;
            case 'info':
                $logger->setLevel(log4php\LoggerLevel::getLevelInfo());
                break;
            case 'debug':
                $logger->setLevel(log4php\LoggerLevel::getLevelDebug());
                break;
            case 'trace':
                $logger->setLevel(log4php\LoggerLevel::getLevelTrace());
                break;
        }

        return $logger;
    }

    /**
     * Log to file
     * 
     * @param string $level error level  
     * @param string $message message to log
     * 
     * @return void
     */
    public function log($level, $message) {
        $localFileLogger = $this->getLogger('localFileLogger');

        if (!class_exists('com\extremeidea\php\tools\log4php\LoggerLoggingEvent')) {
            log4php\LoggerAutoloader::autoload('com\extremeidea\php\tools\log4php\LoggerLoggingEvent');
        }

        if (!class_exists('com\extremeidea\php\tools\log4php\LoggerNDC')) {
            log4php\LoggerAutoloader::autoload('com\extremeidea\php\tools\log4php\LoggerNDC');
        }
        call_user_func_array(array($localFileLogger, $level), array($message));
    }

    /**
     * Log fatal message
     *
     * @param string $message message to log
     *
     * @return void  
     */
    public function fatal($message) {
        $this->log('fatal', $message);
    }

    /**
     * Log error message
     *
     * @param string $message message to log
     *
     * @return void
     */
    public function error($message) {
        $this->log('error', $message);
    }

    /**
     * Log warning message
     *
     * @param string $message message to log
     *
     * @return void
     */
    public function warning($message) {
        $this->log('warn', $message);
    }

    /**
     * Log info message
     *
     * @param string $message message to log
     *
     * @return void
     */
    public function info($message) {
        $this->log('info', $message);
    }

    /**
     * Log debug message
     *
     * @param string $message message to log
     *
     * @return void
     */
    public function debug($message) {
        $this->log('debug', $message);
    }

    /**
     * Log trace message
     * 
     * @param string $message message to log
     *
     * @return void
     */
    public function trace($message) {
        $this->log('trace', $message);
    }
}

