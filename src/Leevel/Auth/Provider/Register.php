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

namespace Leevel\Auth\Provider;

use Leevel\Auth\Auth;
use Leevel\Auth\IAuth;
use Leevel\Auth\Manager;
use Leevel\Auth\Middleware\Auth as MiddlewareAuth;
use Leevel\Di\IContainer;
use Leevel\Di\Provider;

/**
 * auth 服务提供者.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.09.08
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
        $this->auths();
        $this->auth();
    }

    /**
     * 可用服务提供者.
     *
     * @return array
     */
    public static function providers(): array
    {
        return [
            'auths' => Manager::class,
            'auth'  => [IAuth::class, Auth::class],
            MiddlewareAuth::class,
        ];
    }

    /**
     * 是否延迟载入.
     *
     * @return bool
     */
    public static function isDeferred(): bool
    {
        return true;
    }

    /**
     * 注册 auths 服务
     */
    protected function auths(): void
    {
        $this->container
            ->singleton(
                'auths',
                function (IContainer $container): Manager {
                    return new Manager($container);
                },
            );
    }

    /**
     * 注册 auth 服务
     */
    protected function auth(): void
    {
        $this->container
            ->singleton(
                'auth',
                function (IContainer $container): Auth {
                    return $container['auths']->connect();
                },
            );
    }
}
