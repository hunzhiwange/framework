<?php

declare(strict_types=1);

/*
 * This file is part of the ************************ package.
 * _____________                           _______________
 *  ______/     \__  _____  ____  ______  / /_  _________
 *   ____/ __   / / / / _ \/ __`\/ / __ \/ __ \/ __ \___
 *    __/ / /  / /_/ /  __/ /  \  / /_/ / / / / /_/ /__
 *      \_\ \_/\____/\___/_/   / / .___/_/ /_/ .___/
 *         \_\                /_/_/         /_/
 *
 * The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
 * (c) 2010-2019 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Leevel\Support\FunctionNotFoundException;

if (!function_exists('f')) {
    /**
     * 执行惰性加载函数.
     *
     * @param string $fn
     * @param array  ...$args
     *
     * @return mixed
     */
    function f(string $fn, ...$args)
    {
        if (!function_exists($fn)) {
            f_exists($fn);
        }

        return $fn(...$args);
    }
}

if (!function_exists('f_exists')) {
    /**
     * 判断惰性加载函数是否存在.
     *
     * 系统会自动搜索并导入函数文件.
     *
     * @param string $fn
     * @param bool   $throwException
     *
     * @throws \Leevel\Support\FunctionNotFoundException
     *
     * @return bool
     */
    function f_exists(string $fn, bool $throwException = true): bool
    {
        if (function_exists($fn)) {
            return true;
        }

        $virtualClass = null;

        foreach (['fn', 'prefix', 'index'] as $type) {
            switch ($type) {
                case 'fn':
                    $virtualClass = $fn;

                    break;
                case 'prefix':
                    if (false === $position = strpos($fn, '_')) {
                        continue 2;
                    }

                    $virtualClass = substr($fn, 0, $position);

                    break;
                case 'index':
                    if (false === $position = strripos($fn, '\\')) {
                        continue 2;
                    }

                    $virtualClass = substr($fn, 0, $position).'\\index';

                    break;
            }

            if (!$virtualClass) {
                return false;
            }

            class_exists($virtualClass);

            if (function_exists($fn)) {
                return true;
            }
        }

        if (true === $throwException) {
            $e = sprintf('Call to undefined function %s()', $fn);

            throw new FunctionNotFoundException($e);
        }

        return false;
    }
}

if (!function_exists('env')) {
    /**
     * 取得应用的环境变量.支持 bool, empty 和 null.
     *
     * @param mixed $name
     * @param mixed $defaults
     *
     * @return mixed
     */
    function env(string $name, $defaults = null)
    {
        if (false === $value = getenv($name)) {
            $value = $defaults;
        }

        switch ($value) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'empty':
            case '(empty)':
                return '';
            case 'null':
            case '(null)':
                return;
        }

        if (is_string($value) && strlen($value) > 1 &&
            '"' === $value[0] && '"' === $value[strlen($value) - 1]) {
            return substr($value, 1, -1);
        }

        return $value;
    }
}
