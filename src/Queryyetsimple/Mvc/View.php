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
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\Mvc;

use Leevel\View\IConnect as ViewIConnect;

/**
 * 视图.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2016.11.19
 *
 * @version 1.0
 */
class View implements IView
{
    /**
     * 视图模板
     *
     * @var \Leevel\View\IConnect
     */
    protected $theme;

    /**
     * 构造函数.
     *
     * @param \Leevel\View\IConnect $theme
     */
    public function __construct(ViewIConnect $theme)
    {
        $this->theme = $theme;
    }

    /**
     * 切换视图.
     *
     * @param \Leevel\View\IConnect $theme
     *
     * @return $this
     */
    public function switchView(ViewIConnect $theme)
    {
        $assign = $this->getAssign();

        $this->theme = $theme;
        $this->assign($assign);

        return $this;
    }

    /**
     * 变量赋值
     *
     * @param mixed $name
     * @param mixed $value
     *
     * @return $this
     */
    public function assign($name, $value = null)
    {
        $this->theme->setVar($name, $value);

        return $this;
    }

    /**
     * 获取变量赋值
     *
     * @param null|string $name
     *
     * @return mixed
     */
    public function getAssign($name = null)
    {
        return $this->theme->getVar($name);
    }

    /**
     * 删除变量值
     *
     * @param mixed $name
     *
     * @return $this
     */
    public function deleteAssign($name)
    {
        $args = func_get_args();
        $this->theme->deleteVar(...$args);

        return $this;
    }

    /**
     * 清空变量值
     *
     * @param null|string $name
     *
     * @return $this
     */
    public function clearAssign()
    {
        $this->theme->clearVar();

        return $this;
    }

    /**
     * 加载视图文件.
     *
     * @param string $file
     * @param array  $vars
     * @param string $ext
     *
     * @return string
     */
    public function display($file = null, array $vars = [], $ext = null)
    {
        return $this->theme->display($file, $vars, $ext, false);
    }
}
