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
     * 构造函数.
     */
    public function __construct(protected IViews $view)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function switchView(IViews $view): void
    {
        $var = $this->getVar();
        $this->view = $view;
        $this->setVar($var);
    }

    /**
     * {@inheritdoc}
     */
    public function setVar(array|string $name, mixed $value = null): void
    {
        $this->view->setVar($name, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getVar(?string $name = null): mixed
    {
        return $this->view->getVar($name);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteVar(array $name): void
    {
        $this->view->deleteVar($name);
    }

    /**
     * {@inheritdoc}
     */
    public function clearVar(): void
    {
        $this->view->clearVar();
    }

    /**
     * {@inheritdoc}
     */
    public function display(string $file, array $vars = [], ?string $ext = null): string
    {
        return $this->view->display($file, $vars, $ext);
    }
}
