<?php

declare(strict_types=1);

namespace Leevel\Server;

use Leevel\Http\Request;
use Leevel\Router\IRouter;
use Swoole\Coroutine;
use Swoole\Coroutine\Http\Server as SwooleHttpServer;
use Swoole\Http\Request as SwooleHttpRequest;
use Swoole\Http\Response as SwooleHttpResponse;
use Swoole\WebSocket\CloseFrame;
use Swoole\WebSocket\Frame;

/**
 * Websocket 服务.
 */
class Websocket extends Http
{
    /**
     * 新客户接入回调.
     */
    public const string OPEN = 'open';

    /**
     * 收到客户端数据帧回调.
     */
    public const string MESSAGE = 'message';

    /**
     * 客户端关闭回调.
     */
    public const string CLOSE = 'close';

    /**
     * 客户连接 pathInfo 前缀
     */
    public const string PATHINFO = 'websocket_pathinfo_';

    /**
     * 配置.
     */
    public array $config = [
        // 监听 IP 地址
        // https://wiki.swoole.com/#/coroutine/http_server
        'host' => '0.0.0.0',

        // 监听端口
        // https://wiki.swoole.com/#/coroutine/http_server
        'port' => '9501',

        // 是否启用 SSL/TLS 隧道加密
        // https://wiki.swoole.com/#/coroutine/http_server
        'ssl' => false,

        // 进程名称
        'process_name' => 'SERVER.WEBSOCKET',

        // 进程保存路径
        'pid_path' => '',

        // 设置启动的 worker 进程数
        'worker_num' => 8,

        // 自定义进程
        'custom_process' => [],

        // 服务配置
        'settings' => [],
    ];

    /**
     * 客户连接.
     */
    protected array $connections = [];

    /**
     * 获取客户连接.
     */
    public function getConnections(): array
    {
        return $this->connections;
    }

    /**
     * WebSocket 客户端与服务器建立连接并完成握手后.
     */
    public function onOpen(SwooleHttpRequest $swooleRequest, SwooleHttpResponse $swooleResponse, int $workerId): void
    {
        $this->setClientPathInfo($swooleRequest->fd, $swooleRequest->server['path_info']);
        $request = $this->normalizeRequest($swooleRequest);
        $request->setPathInfo($this->normalizePathInfo($request->getPathInfo(), self::OPEN));
        $this->setPreRequestMatched($request, [$this, $swooleRequest, $swooleResponse, $workerId]);
        $this->dispatchRouter($request);
    }

    /**
     * 监听服务器收到来自客户端的数据帧.
     */
    public function onMessage(SwooleHttpRequest $swooleRequest, SwooleHttpResponse $swooleResponse, int $workerId, Frame $frame): void
    {
        if (!($pathInfo = $this->getClientPathInfo($swooleRequest->fd))) {
            return;
        }

        $request = $this->createRequestWithPathInfo($pathInfo, self::MESSAGE);
        $this->setPreRequestMatched($request, [$this, $swooleRequest, $swooleResponse, $frame, $workerId]);
        $this->dispatchRouter($request);
    }

    /**
     * 监听连接关闭事件.
     *
     * - 每个浏览器连接关闭时执行一次, reload 时连接不会断开, 也就不会触发该事件.
     */
    public function onClose(SwooleHttpRequest $swooleRequest, SwooleHttpResponse $swooleResponse, int $workerId, CloseFrame|false|Frame|string $frame): void
    {
        if (!($pathInfo = $this->getClientPathInfo($swooleRequest->fd))) {
            return;
        }

        $request = $this->createRequestWithPathInfo($pathInfo, self::CLOSE);
        $this->setPreRequestMatched($request, [$this, $swooleRequest, $swooleResponse, $frame, $workerId]);
        $this->dispatchRouter($request);
    }

    protected function runRequest(SwooleHttpServer $server, int $workerId): void
    {
        if (self::class === static::class) {
            $this->onWebsocketRequest($server, $workerId);
        } else {
            $this->onHttpRequest($server, $workerId);
        }
    }

    protected function onWebsocketRequest(SwooleHttpServer $server, int $workerId): void
    {
        $server->handle('/', function (SwooleHttpRequest $request, SwooleHttpResponse $response) use ($workerId): void {
            $this->processStatisticTable->incr((string) $workerId, 'request_count');
            Coroutine::create(function () use ($request, $response, $workerId): void {
                $header = $request->header;
                if (!empty($header['connection'])
                    && !empty($header['upgrade'])
                    && 'Upgrade' === $header['connection']
                    && 'websocket' === $header['upgrade']) {
                    $this->onHandShake($request, $response, $workerId);
                } else {
                    $this->onRequest($request, $response);
                }
            });
        });
    }

    protected function onHandShake(SwooleHttpRequest $swooleRequest, SwooleHttpResponse $swooleResponse, int $workerId): void
    {
        if (!$swooleResponse->upgrade()) {
            return;
        }

        $this->connections[spl_object_id($swooleResponse)] = $swooleResponse;

        // @phpstan-ignore-next-line
        $this->onOpen($swooleRequest, $swooleResponse, $workerId);

        while (true) {
            /** @phpstan-ignore-next-line */
            $frame = $swooleResponse->recv(-1);

            if ('' === $frame) {
                $this->close($swooleRequest, $swooleResponse, $workerId, $frame);

                break;
            }

            if (false === $frame) {
                $this->close($swooleRequest, $swooleResponse, $workerId, $frame);

                break;
            }

            // @phpstan-ignore-next-line
            if ('close' === $frame->data || CloseFrame::class === $frame::class) {
                $this->close($swooleRequest, $swooleResponse, $workerId, $frame);

                break;
            }

            // @phpstan-ignore-next-line
            $this->onMessage($swooleRequest, $swooleResponse, $workerId, $frame);
        }
    }

    /**
     * 关闭连接.
     */
    protected function close(SwooleHttpRequest $swooleRequest, SwooleHttpResponse $swooleResponse, int $workerId, CloseFrame|false|Frame|string $frame): void
    {
        // @phpstan-ignore-next-line
        $this->onClose($swooleRequest, $swooleResponse, $workerId, $frame);
        unset($this->connections[$swooleRequest->fd]);
        $swooleResponse->close();
    }

    /**
     * 设置路由匹配数据.
     */
    protected function setPreRequestMatched(Request $request, array $data): void
    {
        /** @var IRouter $router */
        $router = $this->container->make(IRouter::class);
        $router->setPreRequestMatched($request, [IRouter::VARS => $data]);
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
     * 获取客户端连接 PathInfo.
     */
    protected function getClientPathInfo(int $fd): false|string
    {
        $key = self::PATHINFO.$fd;
        $pathInfo = $this->container->make($key, throw: false);
        if (null === $pathInfo) {
            return false;
        }

        // @phpstan-ignore-next-line
        return $pathInfo;
    }

    /**
     * 设置客户端连接 PathInfo.
     */
    protected function setClientPathInfo(int $fd, string $pathInfo): void
    {
        $this->container->addContextKeys(self::PATHINFO.$fd);
        $this->container->instance(self::PATHINFO.$fd, $pathInfo);
    }
}
