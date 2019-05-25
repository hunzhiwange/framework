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

namespace Leevel\Router\Proxy;

use Leevel\Router\IView as IBaseView;
use Leevel\View\IView as IViews;

/**
 * 代理 view 接口.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2019.05.25
 *
 * @version 1.0
 *
 * @see \Leevel\Router\IView 请保持接口设计的一致性
 */
interface IView
{
    /**
     * 切换视图.
     *
     * @param \Leevel\View\IView $view
     *
     * @return \Leevel\Router\IView
     */
    public static function switchView(IViews $view): IBaseView;

    /**
     * 变量赋值.
     *
     * @param mixed $name
     * @param mixed $value
     *
     * @return \Leevel\Router\IView
     */
    public static function setVar($name, $value = null): IBaseView;

    /**
     * 获取变量赋值.
     *
     * @param null|string $name
     *
     * @return mixed
     */
    public static function getVar(?string $name = null);

    /**
     * 删除变量值.
     *
     * @param array $name
     *
     * @return \Leevel\Router\IView
     */
    public static function deleteVar(array $name): IBaseView;

    /**
     * 清空变量值.
     *
     * @return \Leevel\Router\IView
     */
    public static function clearVar(): IBaseView;

    /**
     * 加载视图文件.
     *
     * @param string $file
     * @param array  $vars
     * @param string $ext
     *
     * @return string
     */
    public static function display(string $file, array $vars = [], ?string $ext = null): string;
}
