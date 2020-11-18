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

namespace Leevel\Protocol;

use Leevel\Http\Request;
use Leevel\Router\IRouter;
use Swoole\Http\Request as SwooleHttpRequest;
use Swoole\Websocket\Frame as SwooleWebsocketFrame;
use Swoole\Websocket\Server as SwooleWebsocketServer;

/**
 * Websocket 服务.
 *
 * @see https://wiki.swoole.com/wiki/page/397.html
 */
class WebsocketServer extends HttpServer implements IServer
{
    /**
     * 新客户接入回调.
    */
    const OPEN = 'open';

    /**
     * 收到客户端数据帧回调.
    */
    const MESSAGE = 'message';

    /**
     * 客户端关闭回调.
    */
    const CLOSE = 'close';

    /**
     * 客户连接 pathInfo 前缀
    */
    const PATHINFO = 'websocket_pathinfo_';

    /**
     * 配置.
     */
    public array $option = [
        // 监听 IP 地址
        // see https://wiki.swoole.com/wiki/page/p-server.html
        // see https://wiki.swoole.com/wiki/page/327.html
        'host' => '0.0.0.0',

        // 监听端口
        // see https://wiki.swoole.com/wiki/page/p-server.html
        // see https://wiki.swoole.com/wiki/page/327.html
        'port' => '9501',

        // Swoole 进程名称
        'process_name' => 'leevel.websocket',

        // Swoole 进程保存路径
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
     */
    protected array $serverEvent = [
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
     * WebSocket 客户端与服务器建立连接并完成握手后.
     *
     * @see https://wiki.swoole.com/wiki/page/401.html
     */
    public function onOpen(SwooleWebsocketServer $server, SwooleHttpRequest $swooleRequest): void
    {
        $message = sprintf('Server: handshake success with fd %s', $swooleRequest->fd);
        $this->log($message);

        $this->releaseRootCoroutineData();
        $this->setClientPathInfo($swooleRequest->fd, $swooleRequest->server['path_info']);
        $request = $this->normalizeRequest($swooleRequest);
        $request->setPathInfo($this->normalizePathInfo($request->getPathInfo(), self::OPEN));
        $this->setPreRequestMatched($request, [$server, $request, $swooleRequest->fd]);
        $this->dispatchRouter($request);
    }

    /**
     * 监听服务器收到来自客户端的数据帧.
     *
     * @see https://wiki.swoole.com/wiki/page/397.html
     */
    public function onMessage(SwooleWebsocketServer $server, SwooleWebsocketFrame $frame): void
    {
        $message = sprintf(
            'Receive from fd %d:%s,opcode:%d,fin:%d',
            $frame->fd,
            $frame->data,
            $frame->opcode,
            $frame->finish
        );
        $this->log($message);

        if (false === ($pathInfo = $this->getClientPathInfo($frame->fd))) {
            return;
        }

        $this->releaseRootCoroutineData();
        $request = $this->createRequestWithPathInfo($pathInfo, self::MESSAGE);
        $this->setPreRequestMatched($request, [$server, $frame, $frame->fd]);
        $this->dispatchRouter($request);
    }

    /**
     * 监听连接关闭事件.
     *
     * - 每个浏览器连接关闭时执行一次, reload 时连接不会断开, 也就不会触发该事件.
     *
     * @see https://wiki.swoole.com/wiki/page/p-event/onClose.html
     */
    public function onWebsocketClose(SwooleWebsocketServer $server, int $fd, int $reactorId): void
    {
        $message = sprintf('Server close, fd %d, reactorId %d.', $fd, $reactorId);
        $this->log($message);

        /**
         * 未连接.
         *
         * - WEBSOCKET_STATUS_CONNECTION = 1，连接进入等待握手
         * - WEBSOCKET_STATUS_HANDSHAKE = 2，正在握手
         * - WEBSOCKET_STATUS_FRAME = 3，已握手成功等待浏览器发送数据帧.
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

        $this->releaseRootCoroutineData();
        $request = $this->createRequestWithPathInfo($pathInfo, self::CLOSE);
        $this->setPreRequestMatched($request, [$server, $fd, $reactorId]);
        $this->dispatchRouter($request);
    }

    /**
     * 获取客户端连接数.
     */
    public function count(): int
    {
        return count($this->server->connections);
    }

    /**
     * 设置路由匹配数据.
     */
    protected function setPreRequestMatched(Request $request, array $data): void
    {
        $this->container
            ->make(IRouter::class)
            ->setPreRequestMatched($request, [IRouter::VARS => $data]);
    }

    /**
     * 根据 pathInfo 创建 HTTP 请求对象.
     */
    protected function createRequestWithPathInfo(string $pathInfo, string $type): Request
    {
        $request = new Request();
        $request->setPathInfo($this->normalizePathInfo($pathInfo, $type));

        return $request;
    }

    /**
     * 格式化 pathInfo.
     */
    protected function normalizePathInfo(string $pathInfo, string $type): string
    {
        return '/'.trim($pathInfo, '/').'/'.$type;
    }

    /**
     * 创建 Swoole 服务.
     */
    protected function createSwooleServer(): void
    {
        $this->server = new SwooleWebsocketServer(
            (string) $this->option['host'],
            (int) ($this->option['port'])
        );
        $this->initSwooleServer();
    }

    /**
     * 获取客户端连接 PathInfo.
     */
    protected function getClientPathInfo(int $fd): bool|string
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
     */
    protected function setClientPathInfo(int $fd, string $pathInfo): void
    {
        $this->container->instance(self::PATHINFO.$fd, $pathInfo);
    }
}
