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
use Leevel\Router\IView as IBaseView;
use Leevel\Router\View as BaseView;
use Leevel\View\IView as IViews;

/**
 * 代理 view.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.06.10
 *
 * @version 1.0
 * @codeCoverageIgnore
 */
class View
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
     * 切换视图.
     */
    public static function switchView(IViews $view): IBaseView
    {
        return self::proxy()->switchView($view);
    }

    /**
     * 变量赋值.
     *
     * @param mixed      $name
     * @param null|mixed $value
     */
    public static function setVar($name, $value = null): IBaseView
    {
        return self::proxy()->setVar($name, $value);
    }

    /**
     * 获取变量赋值.
     *
     * @return mixed
     */
    public static function getVar(?string $name = null)
    {
        return self::proxy()->getVar($name);
    }

    /**
     * 删除变量值.
     */
    public static function deleteVar(array $name): IBaseView
    {
        return self::proxy()->deleteVar($name);
    }

    /**
     * 清空变量值.
     */
    public static function clearVar(): IBaseView
    {
        return self::proxy()->clearVar();
    }

    /**
     * 加载视图文件.
     */
    public static function display(string $file, array $vars = [], ?string $ext = null): string
    {
        return self::proxy()->display($file, $vars, $ext);
    }

    /**
     * 代理服务.
     */
    public static function proxy(): BaseView
    {
        return Container::singletons()->make('view');
    }
}
