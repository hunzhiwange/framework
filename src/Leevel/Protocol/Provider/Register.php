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

namespace Leevel\Protocol\Provider;

use Leevel\Di\IContainer;
use Leevel\Di\Provider;
use Leevel\Kernel\IKernel;
use Leevel\Protocol\HttpServer;
use Leevel\Protocol\Pool;
use Leevel\Protocol\RpcServer;
use Leevel\Protocol\Server;
use Leevel\Protocol\WebsocketServer;

/**
 * swoole 服务提供者.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.12.21
 *
 * @version 1.0
 */
class Register extends Provider
{
    /**
     * 是否延迟载入.
     *
     * @var bool
     */
    public static $defer = true;

    /**
     * 注册服务
     */
    public function register()
    {
        $this->swooleServer();
        $this->swooleHttpServer();
        $this->swooleWebsocketServer();
        $this->swooleRpcServer();
        $this->pool();
    }

    /**
     * 可用服务提供者.
     *
     * @return array
     */
    public static function providers(): array
    {
        return [
            'swoole.default.server' => [
                'Leevel\\Protocol\\Server',
            ],
            'swoole.http.server' => [
                'Leevel\\Protocol\\Http\\Server',
            ],
            'swoole.websocket.server' => [
                'Leevel\\Protocol\\Websocket\\Server',
            ],
            'swoole.rpc.server' => [
                'Leevel\\Protocol\\RpcServer',
            ],
            'pool' => [
                'Leevel\\Protocol\\Pool',
                'Leevel\\Protocol\\IPool',
            ],
        ];
    }

    /**
     * 注册 swoole 服务
     */
    protected function swooleServer()
    {
        $this->container->singleton('swoole.default.server', function (IContainer $container) {
            return new Server(
                $container,
                $container['option']['swoole\\server']
            );
        });
    }

    /**
     * 注册 swoole http 服务
     */
    protected function swooleHttpServer()
    {
        $this->container->singleton('swoole.http.server', function (IContainer $container) {
            return new HttpServer(
                $container,
                $container->make(IKernel::class),
                $container['request'],
                array_merge(
                    $container['option']['swoole\\server'],
                    $container['option']['swoole\\http_server']
                )
            );
        });
    }

    /**
     * 注册 swoole websocket 服务
     */
    protected function swooleWebsocketServer()
    {
        $this->container->singleton('swoole.websocket.server', function (IContainer $container) {
            return new WebsocketServer(
                $container,
                $container->make(IKernel::class),
                $container['request'],
                array_merge(
                    $container['option']['swoole\\server'],
                    $container['option']['swoole\\websocket_server']
                )
            );
        });
    }

    /**
     * 注册 swoole rpc 服务
     */
    protected function swooleRpcServer()
    {
        $this->container->singleton('swoole.rpc.server', function (IContainer $container) {
            return new RpcServer(
                $container,
                array_merge(
                    $container['option']['swoole\\server'],
                    $container['option']['swoole\\rpc_server']
                )
            );
        });
    }

    /**
     * 注册 pool 服务
     */
    protected function pool()
    {
        $this->container->singleton('pool', function (IContainer $container) {
            return new Pool($container);
        });
    }
}
