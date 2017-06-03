<?php
/**
 * RoxPHP
 *
 * Copyright (C) 2008 - 2012 Ramon Torres
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) 2008 - 2012 Ramon Torres
 * @package App
 * @license The MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

namespace Rox\Utils;

class Environment
{
    protected static $current;

    public static function detect(\Closure $detector = null)
    {
        if (!$detector) {
            $detector = static::defaultDetector();
        }

        static::$current = $detector->__invoke();

        return static::$current;
    }

    public static function is($environment)
    {
        return (static::$current === $environment);
    }

    public static function set($environment)
    {
        static::$current = $environment;
    }

    public static function get()
    {
        return static::$current;
    }

    protected static function defaultDetector()
    {
        return function () {
            $env = getenv('PHP_ROXPHP_ENV');
            if (!empty($env)) {
                return $env;
            }

            return 'development';
        };
    }
}
