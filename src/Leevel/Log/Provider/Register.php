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

namespace Leevel\Log\Provider;

use Leevel\Di\IContainer;
use Leevel\Di\Provider;
use Leevel\Log\ILog;
use Leevel\Log\Log;
use Leevel\Log\Manager;
use Leevel\Log\Middleware\Log as MiddlewareLog;

/**
 * log 服务提供者.
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
     * 注册服务.
     */
    public function register(): void
    {
        $this->logs();
        $this->log();
        $this->middleware();
    }

    /**
     * 可用服务提供者.
     */
    public static function providers(): array
    {
        return [
            'logs' => Manager::class,
            'log'  => [ILog::class, Log::class],
            MiddlewareLog::class,
        ];
    }

    /**
     * 注册 logs 服务.
     */
    protected function logs(): void
    {
        $this->container
            ->singleton(
                'logs',
                fn (IContainer $container): Manager => new Manager($container),
            );
    }

    /**
     * 注册 log 服务.
     */
    protected function log(): void
    {
        $this->container
            ->singleton(
                'log',
                fn (IContainer $container): ILog => $container['logs']->connect(),
            );
    }

    /**
     * 注册 middleware 服务.
     */
    protected function middleware(): void
    {
        $this->container->singleton(MiddlewareLog::class);
    }
}
