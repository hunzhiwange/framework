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

use Leevel\Di\Container;
use Leevel\Kernel\Proxy\App as ProxyApp;

/**
 * 代理 app 别名.
 */
class App extends ProxyApp
{
}

/**
 * 代理 app 别名.
 */
class Leevel extends ProxyApp
{
}

if (!function_exists('__')) {
    /**
     * 获取语言.
     *
     * @param array ...$data
     */
    function __(string $text, ...$data): string
    {
        /** @var \Leevel\I18n\I18n $service */
        $service = Container::singletons()->make('i18n');

        return $service->gettext($text, ...$data);
    }
}

if (!function_exists('func')) {
    /**
     * 执行惰性加载函数.
     *
     * @param \Closure $fn
     * @param array    ...$args
     *
     * @return mixed
     */
    function func(Closure $fn, ...$args)
    {
        try {
            return $fn(...$args);
        } catch (Error $e) {
            if (false !== strpos($message = $e->getMessage(), $error = 'Call to undefined function ')) {
                class_exists($fnName = str_replace([$error, '()'], '', $message));
                if (!func_exists($fnName)) {
                    throw $e;
                }

                return $fn(...$args);
            }

            throw $e;
        }
    }
}

if (!function_exists('func_exists')) {
    /**
     * 判断惰性加载函数是否存在.
     *
     * - 系统会自动搜索并导入函数文件.
     */
    function func_exists(string $fn): bool
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

        return false;
    }
}
