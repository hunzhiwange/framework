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

namespace Leevel\Session\Provider;

use Leevel\Di\IContainer;
use Leevel\Di\Provider;
use Leevel\Session\ISession;
use Leevel\Session\Manager;
use Leevel\Session\Middleware\Session as MiddlewareSession;
use Leevel\Session\Session;

/**
 * session 服务提供者.
 */
class Register extends Provider
{
    /**
     * {@inheritdoc}
     */
    public function register(): void
    {
        $this->sessions();
        $this->session();
        $this->middleware();
    }

    /**
     * {@inheritdoc}
     */
    public static function providers(): array
    {
        return [
            'sessions' => Manager::class,
            'session'  => [ISession::class, Session::class],
            MiddlewareSession::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function isDeferred(): bool
    {
        return true;
    }

    /**
     * 注册 sessions 服务.
     */
    protected function sessions(): void
    {
        $this->container
            ->singleton(
                'sessions',
                fn (IContainer $container): Manager => new Manager($container),
            );
    }

    /**
     * 注册 session 服务.
     */
    protected function session(): void
    {
        $this->container
            ->singleton(
                'session',
                fn (IContainer $container): ISession => $container['sessions']->connect(),
            );
    }

    /**
     * 注册 middleware 服务.
     */
    protected function middleware(): void
    {
        $this->container->singleton(MiddlewareSession::class);
    }
}
