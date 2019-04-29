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

if (!function_exists('fn')) {
    /**
     * 执行函数.
     *
     * @param string $fn
     * @param array  $args
     *
     * @return mixed
     */
    function fn(string $fn, ...$args)
    {
        if (!function_exists($fn)) {
            fns($fn);
        }

        return $fn(...$args);
    }
}

if (!function_exists('fns')) {
    /**
     * 自动导入函数文件.
     *
     * @param string $fn
     * @param array  $moreFn
     *
     * @return string
     */
    function fns(string $fn, ...$moreFn): string
    {
        if (function_exists($fn)) {
            foreach ($moreFn as $m) {
                fns($m);
            }

            return $fn;
        }

        foreach (['fn', 'prefix', 'index'] as $type) {
            switch ($type) {
                case 'fn':
                    $virtualClass = $fn;

                    break;
                case 'prefix':
                    if (false === strpos($fn, '_')) {
                        continue 2;
                    }

                    $virtualClass = substr($fn, 0, strpos($fn, '_'));

                    break;
                case 'index':
                    if (false === strpos($fn, '\\')) {
                        continue 2;
                    }

                    $virtualClass = substr($fn, 0, strripos($fn, '\\')).'\\index';

                    break;
            }

            class_exists($virtualClass);

            if (function_exists($fn)) {
                foreach ($moreFn as $m) {
                    fns($m);
                }

                return $fn;
            }
        }

        $e = sprintf('Call to undefined function %s()', $fn);

        throw new FunctionNotFoundException($e);
    }
}
