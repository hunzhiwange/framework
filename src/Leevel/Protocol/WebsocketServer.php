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

namespace Leevel\Protocol;

use Swoole\Http\Request as SwooleHttpRequest;
use Swoole\Websocket\Frame as SwooleWebsocketFrame;
use Swoole\Websocket\Server as SwooleWebsocketServer;

/**
 * Websocket 服务
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.12.30
 * @see https://wiki.swoole.com/wiki/page/397.html
 *
 * @version 1.0
 */
class WebsocketServer extends HttpServer implements IServer
{
    /**
     * 配置.
     *
     * @var array
     */
    protected $option = [
        // 监听 IP 地址
        // see https://wiki.swoole.com/wiki/page/p-server.html
        // see https://wiki.swoole.com/wiki/page/327.html
        'host' => '0.0.0.0',

        // 监听端口
        // see https://wiki.swoole.com/wiki/page/p-server.html
        // see https://wiki.swoole.com/wiki/page/327.html
        'port' => '9501',

        // swoole 进程名称
        'process_name' => 'leevel.websocket',

        // swoole 进程保存路径
        'pid_path' => '',

        // 设置启动的 worker 进程数
        // see https://wiki.swoole.com/wiki/page/275.html
        'worker_num' => 8,

        // 守护进程化
        // see https://wiki.swoole.com/wiki/page/278.html
        'daemonize' => 0,

        // 自定义进程
        'processes' => [],
    ];

    /**
     * 服务回调事件.
     *
     * @var array
     */
    protected $serverEvent = [
        'start',
        'connect',
        'workerStart',
        'managerStart',
        'workerStop',
        'request',
        'shutdown',
        'open',
        'message',
        'task',
        'finish',
        'close',
    ];

    /**
     * WebSocket客户端与服务器建立连接并完成握手后.
     *
     * @param \Leevel\Di\IContainer    $container
     * @param \Swoole\Websocket\Server $server
     * @param \Swoole\Http\Request     $request
     *
     * @see https://wiki.swoole.com/wiki/page/401.html
     */
    public function onOpen(SwooleWebsocketServer $server, SwooleHttpRequest $request)
    {
        $this->log(
            sprintf(
                'Server: handshake success with fd %s', $request->fd
            )
        );
    }

    /**
     * 监听服务器收到来自客户端的数据帧.
     *
     * @param \Swoole\Websocket\Server $server
     * @param \Swoole\Websocket\Frame  $frame
     *
     * @see https://wiki.swoole.com/wiki/page/397.html
     */
    public function onMessage(SwooleWebsocketServer $server, SwooleWebsocketFrame $frame)
    {
        $this->log(
            sprintf(
                'Receive from fd %d:%s,opcode:%d,fin:%d',
                $frame->fd,
                $frame->data,
                $frame->opcode,
                $frame->finish
            )
        );

        $server->push($frame->fd, 'I am from server.');
    }

    /**
     * 创建 websocket server.
     */
    protected function createServer()
    {
        $this->server = new SwooleWebsocketServer(
            $this->option['host'],
            (int) ($this->option['port'])
        );

        $this->initServer();
    }
}
