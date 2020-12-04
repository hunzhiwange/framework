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

namespace Leevel\View\Proxy;

use Leevel\Di\Container;
use Leevel\View\Manager;

/**
 * 代理 view.
 *
 * @method static string display(string $file, array $vars = [], ?string $ext = null) 加载视图文件. 
 * @method static void setVar(array|string $name, mixed $value = null) 设置模板变量. 
 * @method static mixed getVar(?string $name = null) 获取变量值. 
 * @method static void deleteVar(array $name) 删除变量值. 
 * @method static void clearVar() 清空变量值. 
 */
class View
{
    /**
     * 实现魔术方法 __callStatic.
     */
    public static function __callStatic(string $method, array $args): mixed
    {
        return self::proxy()->{$method}(...$args);
    }

    /**
     * 代理服务.
     */
    public static function proxy(): Manager
    {
        return Container::singletons()->make('views');
    }
}
