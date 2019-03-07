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

namespace Leevel\Router;

use Leevel\View\IView as IViews;

/**
 * IView 接口.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.04.23
 *
 * @version 1.0
 */
interface IView
{
    /**
     * 切换视图.
     *
     * @param \Leevel\View\IView $theme
     *
     * @return $this
     */
    public function switchView(IViews $theme): self;

    /**
     * 变量赋值.
     *
     * @param mixed $name
     * @param mixed $value
     *
     * @return $this
     */
    public function assign($name, $value = null): self;

    /**
     * 获取变量赋值.
     *
     * @param null|string $name
     *
     * @return mixed
     */
    public function getAssign(?string $name = null);

    /**
     * 删除变量值.
     *
     * @param array $name
     *
     * @return $this
     */
    public function deleteAssign(array $name): self;

    /**
     * 清空变量值.
     *
     * @return $this
     */
    public function clearAssign(): self;

    /**
     * 加载视图文件.
     *
     * @param string $file
     * @param array  $vars
     * @param string $ext
     *
     * @return string
     */
    public function display(string $file, array $vars = [], ?string $ext = null): string;
}
