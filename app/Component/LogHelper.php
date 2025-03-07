<?php

namespace App\Component;

use App\Component\Exception\DebugException;
use App\Component\Exception\ErrorException;
use App\Component\Exception\InfoException;
use App\Component\Exception\WarningException;
use Illuminate\Support\Facades\Log;

class LogHelper
{
    /**
     * Custom log record helper method (used to record unrecoverable errors)
     * @param $message
     * @param $e
     * @return void
     */
    public static function error($message, $e = null): void
    {
        self::errorHandle('error', $message, $e);
    }

    /**
     * Custom log record helper method (used to record some warning information that does not affect the execution of the application)
     * @param $message
     * @param $e
     * @return void
     */
    public static function warning($message, $e = null): void
    {
        self::errorHandle('warning', $message, $e);
    }

    /**
     * Custom log record helper method, record user debugging information
     * @param $message
     * @param $e
     * @return void
     */
    public static function debug($message, $e = null): void
    {
        self::errorHandle('debug', $message, $e);
    }

    /**
     * Custom log record helper method, record user debugging information
     * @param $message
     * @param $e
     * @return void
     */
    public static function info($message, $e = null): void
    {
        self::errorHandle('info', $message, $e);
    }

    /**
     * Custom log record helper method
     * @param $exceptionType
     * @param $message
     * @param $e
     * @return void
     */
    protected static function errorHandle($exceptionType, $message, $e = null): void
    {
        $basicExceptType = '';
        if ($e instanceof ErrorException) {
            $basicExceptType = 'error';
        } elseif ($e instanceof WarningException) {
            $basicExceptType = 'warning';
        } elseif ($e instanceof DebugException) {
            $basicExceptType = 'debug';
        } elseif ($e instanceof InfoException) {
            $basicExceptType = 'info';
        }
        if (empty($basicExceptType) === false) {
            self::writeLog($basicExceptType, $message);
        } else {
            self::writeLog($exceptionType, $message);
        }
    }

    /**
     * Custom log record helper method
     * @param $exceptionType
     * @param $message
     * @return void
     */
    protected static function writeLog($exceptionType, $message): void
    {
        switch ($exceptionType) {
            case 'error' :
                Log::error($message);
                break;
            case 'warning' :
                Log::warning($message);
                break;
            case 'debug' :
                Log::debug($message);
                break;
            case 'info' :
                Log::info($message);
                break;
            default:
                Log::error($message);
        }
    }
}
