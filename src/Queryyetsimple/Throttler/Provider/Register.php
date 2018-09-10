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

namespace Leevel\Throttler\Provider;

use Leevel\Di\IContainer;
use Leevel\Di\Provider;
use Leevel\Throttler\Throttler;

/**
 * throttler 服务提供者.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.08.09
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
        $this->throttler();
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
            'throttler' => [
                'Leevel\\Throttler\\Throttler',
                'Leevel\\Throttler\\IThrottler',
            ],
            'Leevel\\Throttler\\Middleware\\Throttler',
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
     * 注册 throttler 服务
     */
    protected function throttler()
    {
        $this->container->singleton('throttler', function (IContainer $container) {
            return (new Throttler($container['caches']->
            connect($container['option']['throttler\\driver'])))->

            setRequest($container['request']);
        });
    }

    /**
     * 注册 middleware 服务
     */
    protected function middleware()
    {
        $this->container->singleton('Leevel\\Throttler\\Middleware\\Throttler');
    }
}
