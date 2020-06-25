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
 * (c) 2010-2020 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Closure;
use Error;
use Leevel\Support\FunctionNotFoundException;

if (!function_exists('f')) {
    /**
     * 执行惰性加载函数.
     *
     * @param \Closure|string $fn
     * @param array           ...$args
     *
     * @return mixed
     */
    function f($fn, ...$args)
    {
        if ($fn instanceof Closure) {
            try {
                return $fn(...$args);
            } catch (Error $e) {
                if (false !== strpos($message = $e->getMessage(), $error = 'Call to undefined function ')) {
                    f_exists(str_replace([$error, '()'], '', $message));

                    return $fn(...$args);
                }

                throw $e;
            }
        }

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
     * @throws \Leevel\Support\FunctionNotFoundException
     */
    function f_exists(string $fn, bool $throwException = true): bool
    {
        if (function_exists($fn)) {
            return true;
        }

        $virtualClass = '';
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
                default:
                    if (false === $position = strripos($fn, '\\')) {
                        continue 2;
                    }

                    $virtualClass = substr($fn, 0, $position).'\\index';

                    break;
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
