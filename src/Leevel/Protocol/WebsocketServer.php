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

use Leevel\Http\IRequest;
use Leevel\Http\Request;
use Leevel\Router\IRouter;
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
     * 新客户接入回调.
     *
     * @var string
     */
    const OPEN = 'open';

    /**
     * 收到客户端数据帧回调.
     *
     * @var string
     */
    const MESSAGE = 'message';

    /**
     * 客户端关闭回调.
     *
     * @var string
     */
    const CLOSE = 'close';

    /**
     * 客户连接 pathInfo 前缀
     *
     * @var string
     */
    const PATHINFO = 'websocket_pathinfo_';

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
     * @param \Swoole\Websocket\Server $server
     * @param \Swoole\Http\Request     $swooleRequest
     *
     * @see https://wiki.swoole.com/wiki/page/401.html
     */
    public function onOpen(SwooleWebsocketServer $server, SwooleHttpRequest $swooleRequest): void
    {
        $this->log(
            sprintf('Server: handshake success with fd %s', $swooleRequest->fd)
        );

        $this->setClientPathInfo($swooleRequest->fd, $swooleRequest->server['path_info']);

        $request = $this->normalizeRequest($swooleRequest);
        $request->setPathInfo($this->normalizePathInfo($request->getPathInfo(), self::OPEN));

        $this->setRouterMatchedData([$server, $request, $swooleRequest->fd]);

        $response = $this->dispatchRouter($request);
    }

    /**
     * 监听服务器收到来自客户端的数据帧.
     *
     * @param \Swoole\Websocket\Server $server
     * @param \Swoole\Websocket\Frame  $frame
     *
     * @see https://wiki.swoole.com/wiki/page/397.html
     */
    public function onMessage(SwooleWebsocketServer $server, SwooleWebsocketFrame $frame): void
    {
        $this->log(
            sprintf(
                'Receive from fd %d:%s,opcode:%d,fin:%d',
                $frame->fd, $frame->data, $frame->opcode, $frame->finish
            )
        );

        if (false === ($pathInfo = $this->getClientPathInfo($frame->fd))) {
            return;
        }

        $this->setRouterMatchedData([$server, $frame, $frame->fd]);

        $request = $this->createRequestWithPathInfo($pathInfo, self::MESSAGE);

        $response = $this->dispatchRouter($request);
    }

    /**
     * 监听连接关闭事件
     * 每个浏览器连接关闭时执行一次, reload 时连接不会断开, 也就不会触发该事件.
     *
     * @param \Swoole\Websocket\Server $server
     * @param int                      $fd
     * @param int                      $reactorId
     *
     * @see https://wiki.swoole.com/wiki/page/p-event/onClose.html
     */
    public function onWebsocketClose(SwooleWebsocketServer $server, int $fd, int $reactorId): void
    {
        $this->log(
            sprintf('Server close, fd %d, reactorId %d.', $fd, $reactorId)
        );

        /**
         * 未连接
         * WEBSOCKET_STATUS_CONNECTION = 1，连接进入等待握手
         * WEBSOCKET_STATUS_HANDSHAKE = 2，正在握手
         * WEBSOCKET_STATUS_FRAME = 3，已握手成功等待浏览器发送数据帧.
         *
         * @see https://wiki.swoole.com/wiki/page/413.html
         */
        $clientInfo = $this->server->getClientInfo($fd);

        if ($clientInfo['websocket_status'] <= 0) {
            return;
        }

        // 不存在任何连接
        if (!$this->count()) {
            return;
        }

        if (false === ($pathInfo = $this->getClientPathInfo($fd))) {
            return;
        }

        $request = $this->createRequestWithPathInfo($pathInfo, self::CLOSE);

        $this->setRouterMatchedData([$server, $fd, $reactorId]);

        $response = $this->dispatchRouter($request);
    }

    /**
     * 获取客户端连接数.
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->server->connections);
    }

    /**
     * 设置路由匹配数据.
     *
     * @param array $data
     */
    protected function setRouterMatchedData(array $data): void
    {
        $this->container->make(IRouter::class)->

        setMatchedData([IRouter::VARS => $data]);
    }

    /**
     * 根据 pathInfo 创建 HTTP 请求对象
     *
     * @param string $pathInfo
     * @param string $type
     *
     * @return \Leevel\Http\IRequest
     */
    protected function createRequestWithPathInfo(string $pathInfo, string $type): IRequest
    {
        $request = new Request();
        $request->setPathInfo($this->normalizePathInfo($pathInfo, $type));

        return $request;
    }

    /**
     * 格式化 pathInfo.
     *
     * @param string $pathInfo
     * @param string $type
     *
     * @return string
     */
    protected function normalizePathInfo(string $pathInfo, string $type): string
    {
        return '/'.trim($pathInfo, '/').'/'.$type;
    }

    /**
     * 创建 websocket server.
     */
    protected function createServer(): void
    {
        $this->server = new SwooleWebsocketServer(
            $this->option['host'],
            (int) ($this->option['port'])
        );

        $this->initServer();
    }

    /**
     * 获取客户端连接 PathInfo.
     *
     * @param int $fd
     *
     * @return false|string
     */
    protected function getClientPathInfo(int $fd)
    {
        $key = self::PATHINFO.$fd;
        $pathInfo = $this->container->make($key);

        if ($key === $pathInfo) {
            return false;
        }

        return $pathInfo;
    }

    /**
     * 设置客户端连接 PathInfo.
     *
     * @param int    $fd
     * @param string $pathInfo
     */
    protected function setClientPathInfo(int $fd, string $pathInfo): void
    {
        $this->container->instance(self::PATHINFO.$fd, $pathInfo);
    }
}
