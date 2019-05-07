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

namespace Leevel\Debug\Provider;

use Leevel\Debug\Debug;
use Leevel\Debug\Middleware\Debug as MiddlewareDebug;
use Leevel\Di\IContainer;
use Leevel\Di\Provider;

/**
 * debug 服务提供者.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.09.20
 *
 * @version 1.0
 */
class Register extends Provider
{
    /**
     * 注册服务.
     */
    public function register(): void
    {
        $this->debug();
        $this->middleware();
    }

    /**
     * 可用服务提供者.
     *
     * @return array
     */
    public static function providers(): array
    {
        return [
            'debug' => Debug::class,
            MiddlewareDebug::class,
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
     * 注册 debug 服务.
     */
    protected function debug(): void
    {
        $this->container->singleton('debug', function (IContainer $container): Debug {
            return new Debug($container, $container->make('option')->get('debug\\'));
        });
    }

    /**
     * 注册 middleware 服务.
     */
    protected function middleware(): void
    {
        $this->container->singleton(MiddlewareDebug::class);
    }
}
