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

namespace Leevel\View\Provider;

use Leevel\Di\IContainer;
use Leevel\Di\Provider;
use Leevel\View\Compiler;
use Leevel\View\ICompiler;
use Leevel\View\IParser;
use Leevel\View\IView;
use Leevel\View\Manager;
use Leevel\View\Parser;
use Leevel\View\View;

/**
 * view 服务提供者.
 */
class Register extends Provider
{
    /**
     * 注册服务.
     */
    public function register(): void
    {
        $this->viewViews();
        $this->viewView();
        $this->viewCompiler();
        $this->viewParser();
    }

    /**
     * 可用服务提供者.
     */
    public static function providers(): array
    {
        return [
            'view.views'    => Manager::class,
            'view.view'     => [IView::class, View::class],
            'view.compiler' => [ICompiler::class, Compiler::class],
            'view.parser'   => [IParser::class, Parser::class],
        ];
    }

    /**
     * 是否延迟载入.
     */
    public static function isDeferred(): bool
    {
        return true;
    }

    /**
     * 注册 view.views 服务.
     */
    protected function viewViews(): void
    {
        $this->container
            ->singleton(
                'view.views',
                fn (IContainer $container): Manager => new Manager($container),
            );
    }

    /**
     * 注册 view.view 服务.
     */
    protected function viewView(): void
    {
        $this->container
            ->singleton(
                'view.view',
                fn (IContainer $container): IView => $container['view.views']->connect(),
            );
    }

    /**
     * 注册 view.compiler 服务.
     */
    protected function viewCompiler(): void
    {
        $this->container
            ->singleton(
                'view.compiler',
                fn (): Compiler => new Compiler(),
            );
    }

    /**
     * 注册 view.parser 服务.
     */
    protected function viewParser(): void
    {
        $this->container
            ->singleton(
                'view.parser',
                function (IContainer $container): Parser {
                    return (new Parser($container['view.compiler']))
                        ->registerCompilers()
                        ->registerParsers();
                },
            );
    }
}
