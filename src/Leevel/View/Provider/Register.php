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

namespace Leevel\View\Provider;

use Leevel\Di\IContainer;
use Leevel\Di\Provider;
use Leevel\Kernel\IProject;
use Leevel\View\Compiler;
use Leevel\View\Manager;
use Leevel\View\Parser;
use Twig_Environment;
use Twig_Loader_Filesystem;

/**
 * view 服务提供者.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.05.12
 *
 * @version 1.0
 */
class Register extends Provider
{
    /**
     * 注册服务
     */
    public function register()
    {
        $this->viewViews();
        $this->viewView();
        $this->viewCompiler();
        $this->viewParser();
        $this->viewTwigParser();
    }

    /**
     * 可用服务提供者.
     *
     * @return array
     */
    public static function providers(): array
    {
        return [
            'view.views' => [
                'Leevel\\View\\Manager',
            ],
            'view.view' => [
                'Leevel\\View\\View',
                'Leevel\\View\\IView',
            ],
            'view.compiler' => [
                'Leevel\\View\\Compiler',
                'Leevel\\View\\ICompiler',
            ],
            'view.parser' => [
                'Leevel\\View\\Parser',
                'Leevel\\View\\IParser',
            ],
            'view.twig.parser',
        ];
    }

    /**
     * 注册 view.views 服务
     */
    protected function viewViews(): void
    {
        $this->container->singleton('view.views', function (IContainer $container) {
            return new Manager($container);
        });
    }

    /**
     * 注册 view.view 服务
     */
    protected function viewView(): void
    {
        $this->container->singleton('view.view', function (IContainer $container) {
            return $container['view.views']->connect();
        });
    }

    /**
     * 注册 view.compiler 服务
     */
    protected function viewCompiler(): void
    {
        $this->container->singleton('view.compiler', function (IContainer $container) {
            return new Compiler();
        });
    }

    /**
     * 注册 view.parser 服务
     */
    protected function viewParser(): void
    {
        $this->container->singleton('view.parser', function (IContainer $container) {
            return (new Parser($container['view.compiler']))->
            registerCompilers()->

            registerParsers();
        });
    }

    /**
     * 注册 view.twig.parser 服务
     */
    protected function viewTwigParser(): void
    {
        $this->container->singleton('view.twig.parser', function (IProject $project) {
            return new Twig_Environment(new Twig_Loader_Filesystem(), [
                'auto_reload'      => true,
                'debug'            => false,
                'strict_variables' => true,
                'cache'            => $project->runtimePath('theme'),
            ]);
        });
    }
}
