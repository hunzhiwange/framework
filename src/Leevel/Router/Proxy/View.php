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

namespace Leevel\Router\Proxy;

use Leevel\Di\Container;
use Leevel\Router\View as BaseView;

/**
 * 代理 view.
 *
 * @method static void switchView(\Leevel\View\IView $view)                           切换视图.
 * @method static void setVar($name, $value = null)                                   变量赋值.
 * @method static mixed getVar(?string $name = null)                                  获取变量赋值.
 * @method static void deleteVar(array $name)                                         删除变量值.
 * @method static void clearVar()                                                     清空变量值.
 * @method static string display(string $file, array $vars = [], ?string $ext = null) 加载视图文件.
 */
class View
{
    /**
     * call.
     */
    public static function __callStatic(string $method, array $args): mixed
    {
        return self::proxy()->{$method}(...$args);
    }

    /**
     * 代理服务.
     */
    public static function proxy(): BaseView
    {
        return Container::singletons()->make('view');
    }
}
