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

namespace Leevel\Router;

use Leevel\View\IView as IViews;

/**
 * 视图.
 */
class View implements IView
{
    /**
     * 视图模板.
     *
     * @var \Leevel\View\IView
     */
    protected IViews $view;

    /**
     * 构造函数.
     */
    public function __construct(IViews $view)
    {
        $this->view = $view;
    }

    /**
     * 切换视图.
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
     * 变量赋值.
     *
     * @param mixed      $name
     * @param null|mixed $value
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
     * @return mixed
     */
    public function getVar(?string $name = null)
    {
        return $this->view->getVar($name);
    }

    /**
     * 删除变量值.
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
     * @return \Leevel\Router\IView
     */
    public function clearVar(): IView
    {
        $this->view->clearVar();

        return $this;
    }

    /**
     * 加载视图文件.
     */
    public function display(string $file, array $vars = [], ?string $ext = null): string
    {
        return $this->view->display($file, $vars, $ext, false);
    }
}
