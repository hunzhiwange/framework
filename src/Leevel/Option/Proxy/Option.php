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

namespace Leevel\Option\Proxy;

use Leevel\Di\Container;
use Leevel\Option\Option as BaseOption;

/**
 * 代理 option.
 *
 * @method static bool has(string $name = 'app\\')                    是否存在配置.
 * @method static mixed get(string $name = 'app\\', $defaults = null) 获取配置.
 * @method static array all()                                         返回所有配置.
 * @method static void set($name, $value = null)                      设置配置.
 * @method static void delete(string $name)                           删除配置.
 * @method static void reset($namespaces = null)                      初始化配置参数.
 */
class Option
{
    /**
     * call.
     *
     * @return mixed
     */
    public static function __callStatic(string $method, array $args)
    {
        return self::proxy()->{$method}(...$args);
    }

    /**
     * 代理服务.
     */
    public static function proxy(): BaseOption
    {
        return Container::singletons()->make('option');
    }
}
