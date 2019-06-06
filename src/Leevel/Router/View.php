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
     * @var \Leevel\View\IView
     */
    protected $view;

    /**
     * 构造函数.
     *
     * @param \Leevel\View\IView $view
     */
    public function __construct(IViews $view)
    {
        $this->view = $view;
    }

    /**
     * 切换视图.
     *
     * @param \Leevel\View\IView $view
     *
     * @return \Leevel\Router\IView
     */
    public function switchView(IViews $view): IView
    {
        $var = $this->getVar();

        $this->view = $view;
        $this->setVar($var);

        return $this;
    }

    /**
     * 变量赋值
     *
     * @param mixed $name
     * @param mixed $value
     *
     * @return \Leevel\Router\IView
     */
    public function setVar($name, $value = null): IView
    {
        $this->view->setVar($name, $value);

        return $this;
    }

    /**
     * 获取变量赋值.
     *
     * @param null|string $name
     *
     * @return mixed
     */
    public function getVar(?string $name = null)
    {
        return $this->view->getVar($name);
    }

    /**
     * 删除变量值.
     *
     * @param array $name
     *
     * @return \Leevel\Router\IView
     */
    public function deleteVar(array $name): IView
    {
        $this->view->deleteVar($name);

        return $this;
    }

    /**
     * 清空变量值.
     *
     * @param null|string $name
     *
     * @return \Leevel\Router\IView
     */
    public function clearVar(): IView
    {
        $this->view->clearVar();

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
    public function display(string $file, array $vars = [], ?string $ext = null): string
    {
        return $this->view->display($file, $vars, $ext, false);
    }
}
