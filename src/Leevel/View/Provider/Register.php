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
use Leevel\View\IView;
use Leevel\View\Manager;
use Leevel\View\View;

/**
 * view 服务提供者.
 */
class Register extends Provider
{
    /**
     * {@inheritDoc}
     */
    public function register(): void
    {
        $this->views();
        $this->view();
    }

    /**
     * {@inheritDoc}
     */
    public static function providers(): array
    {
        return [
            'views' => Manager::class,
            'view'  => [IView::class, View::class],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public static function isDeferred(): bool
    {
        return true;
    }

    /**
     * 注册 views 服务.
     */
    protected function views(): void
    {
        $this->container
            ->singleton(
                'views',
                fn (IContainer $container): Manager => new Manager($container),
            );
    }

    /**
     * 注册 view 服务.
     */
    protected function view(): void
    {
        $this->container
            ->singleton(
                'view',
                fn (IContainer $container): IView => $container['views']->connect(),
            );
    }
}
