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

use Leevel\View\IView as IViews;
use RuntimeException;

/**
 * 基类控制器.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2016.11.19
 *
 * @version 1.0
 */
abstract class Controller implements IController
{
    /**
     * 视图.
     *
     * @var \leevel\Mvc\IView
     */
    protected $view;

    /**
     * 构造函数.
     */
    public function __construct()
    {
    }

    /**
     * 设置视图.
     *
     * @param \Leevel\Mvc\IView $view
     *
     * @return $this
     */
    public function setView(IView $view)
    {
        $this->view = $view;

        return $this;
    }

    /**
     * 切换视图.
     *
     * @param \Leevel\View\IViews $theme
     *
     * @return $this
     */
    public function switchView(IViews $theme)
    {
        $this->checkView();
        $this->view->switchView($theme);

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
        $this->checkView();
        $this->view->assign($name, $value);

        return $this;
    }

    /**
     * 获取变量赋值
     *
     * @param null|string $name
     *
     * @return mixed
     */
    public function getAssign(?string $name = null)
    {
        $this->checkView();

        return $this->view->getAssign($name);
    }

    /**
     * 删除变量值
     *
     * @param array $name
     *
     * @return $this
     */
    public function deleteAssign(array $name)
    {
        $this->checkView();

        $this->view->deleteAssign($name);

        return $this;
    }

    /**
     * 清空变量值
     *
     * @return $this
     */
    public function clearAssign()
    {
        $this->checkView();
        $this->view->clearAssign();

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
    public function display(string $file, array $vars = [], ?string $ext = null)
    {
        $this->checkView();

        return $this->view->display($file, $vars, $ext);
    }

    /**
     * 验证 view.
     */
    protected function checkView()
    {
        if (!$this->view) {
            throw new RuntimeException('View is not set in controller.');
        }
    }
}
