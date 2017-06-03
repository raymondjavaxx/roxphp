<?php
/**
 * RoxPHP
 *
 * Copyright (C) 2008 - 2011 Ramon Torres
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) 2008 - 2011 Ramon Torres
 * @package Rox
 * @license The MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

namespace Rox\Log;

use Psr\Log\LoggerInterface;
use Rox\Log\Logger\BlackholeLogger;

/**
 * Logger class
 * 
 * @package Rox
 */
class Log
{
	protected static $logger;

	public static function setLogger(LoggerInterface $logger)
	{
		static::$logger = $logger;
	}

	public static function logger()
	{
		if (static::$logger === null) {
			static::$logger = new BlackholeLogger();
		}

		return static::$logger;
	}

    public static function emergency($message, array $context)
    {
		static::logger()->emergency($message, $context);
    }

    public static function alert($message, array $context)
    {
		static::logger()->alert($message, $context);
    }

    public static function critical($message, array $context)
    {
		static::logger()->critical($message, $context);
    }

    public static function error($message, array $context)
    {
		static::logger()->error($message, $context);
    }

    public static function warning($message, array $context)
    {
		static::logger()->warning($message, $context);
    }

    public static function notice($message, array $context)
    {
		static::logger()->notice($message, $context);
    }

    public static function info($message, array $context)
    {
		static::logger()->info($message, $context);
    }

    public static function debug($message, array $context)
    {
		static::logger()->debug($message, $context);
    }

	public static function write($type, $message, array $context = [])
	{
		static::logger()->log($type, $message, $context);
	}
}
