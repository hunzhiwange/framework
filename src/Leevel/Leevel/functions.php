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

use Leevel\Leevel\App;
use function Leevel\Support\Helper\fn as fns;
use function Leevel\Support\Str\un_camelize;

if (!function_exists('fn')) {
    /**
     * 自动导入函数.
     *
     * @param callable|\Closure|string $fn
     * @param array                    $args
     *
     * @return mixed
     */
    function fn($fn, ...$args)
    {
        return fns($fn, ...$args);
    }
}

if (!function_exists('hl')) {
    /**
     * 助手函数调用.
     *
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     */
    function hl(string $method, ...$args)
    {
        $map = [
            'benchmark' => 'Debug',
            'dd'        => 'Debug',
            'drr'       => 'Debug',
            'dump'      => 'Debug',
            'decrypt'   => 'Encryption',
            'encrypt'   => 'Encryption',
            'gettext'   => 'I18n',
            'app'       => 'Kernel',
            'url'       => 'Router',
            'flash'     => 'Session',
            'env'       => 'Support',
            'value'     => 'Support',
        ];

        $component = $map[$method] ?? ucfirst($method);
        $fn = '\\Leevel\\'.$component.'\\Helper\\'.$method;

        return fn($fn, ...$args);
    }
}

if (!function_exists('app')) {
    /**
     * 返回应用容器或者注入.
     *
     * @param null|string $service
     * @param array       $args
     *
     * @return \Leevel\Leevel\App|mixed
     */
    function app(?string $service = null, array $args = [])
    {
        return fn('Leevel\\Kernel\\Helper\\app', $service, $args);
    }
}

if (!function_exists('__')) {
    /**
     * 获取语言.
     *
     * @param string $text
     * @param array  $arr
     *
     * @return string
     */
    function __(string $text, ...$arr): string
    {
        return fn('Leevel\\I18n\\Helper\\gettext', $text, ...$arr);
    }
}

/**
 * 函数库.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2016.11.26
 *
 * @version 1.0
 */
class Leevel
{
    /**
     * call.
     *
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     */
    public static function __callStatic(string $method, array $args)
    {
        $app = App::singletons();

        if (method_exists($app, $method)) {
            return $app->{$method}(...$args);
        }

        return hl(un_camelize($method), ...$args);
    }
}

// @codeCoverageIgnoreStart
if (!function_exists('Leevel\\Support\\Str\\un_camelize')) {
    include dirname(__DIR__).'/Support/Str/un_camelize.php';
}

if (!function_exists('Leevel\\Support\\Helper\\fn')) {
    include dirname(__DIR__).'/Support/Helper/fn.php';
}
// @codeCoverageIgnoreEnd
