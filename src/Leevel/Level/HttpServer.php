<?php

declare(strict_types=1);

namespace Leevel\Level;

use Leevel\Http\Request;
use Leevel\Kernel\IKernel;
use Swoole\Http\Request as SwooleHttpRequest;
use Swoole\Http\Response as SwooleHttpResponse;
use Swoole\Http\Server as SwooleHttpServer;
use Symfony\Component\HttpFoundation\Response;

/**
 *  HTTP 服务.
 */
class HttpServer extends Server implements IServer
{
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
        'process_name' => 'leevel.http',

        // Swoole 进程保存路径
        'pid_path' => '',

        // 设置启动的 worker 进程数
        // see https://wiki.swoole.com/wiki/page/275.html
        'worker_num' => 8,

        // 守护进程化
        // see https://wiki.swoole.com/wiki/page/278.html
        'daemonize' => 0,

        // 开启静态路径
        // 配合 Nginx 可以设置这里为 false,Nginx 设置规则解析静态路径动态路由转发给 Swoole
        'enable_static_handler' => false,

        // 开启静态路径目录
        'document_root' => '',

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
        'request',
        'receive',
        'task',
        'finish',
        'shutdown',
        'close',
    ];

    /**
     * 处理 HTTP 请求.
     *
     * - 浏览器连接服务器后, 页面上的每个请求均会执行一次
     * - Nginx 反向代理每次打开链接页面默认都是接收两个请求, 一个是正常的数据请求, 一个 favicon.ico 的请求
     * - 可以通过 Nginx deny 屏蔽掉 favicon.ico 的请求，具体请 Google 或者百度.
     */
    public function onRequest(SwooleHttpRequest $swooleRequest, SwooleHttpResponse $swooleResponse): void
    {
        if ($this->isFavicon($swooleRequest)) {
            $swooleResponse->end();

            return;
        }

        $this->releaseRootCoroutineData();
        $request = $this->normalizeRequest($swooleRequest);
        $response = $this->dispatchRouter($request);
        $swooleResponse = $this->normalizeResponse($response, $swooleResponse);
        $swooleResponse->end();
    }

    /**
     * 监听连接关闭事件.
     *
     * - 每个浏览器连接关闭时执行一次, reload 时连接不会断开, 也就不会触发该事件.
     *
     * @see https://wiki.swoole.com/wiki/page/p-event/onClose.html
     */
    public function onHttpClose(SwooleHttpServer $server, int $fd, int $reactorId): void
    {
        $message = sprintf('Server close, fd %d, reactorId %d.', $fd, $reactorId);
        $this->log($message);
    }

    /**
     * 请求过滤 favicon.
     */
    protected function isFavicon(SwooleHttpRequest $swooleRequest): bool
    {
        return '/favicon.ico' === $swooleRequest->server['path_info'] ||
            '/favicon.ico' === $swooleRequest->server['request_uri'];
    }

    /**
     * 路由调度.
     */
    protected function dispatchRouter(Request $request): Response
    {
        $kernel = $this->container->make(IKernel::class);
        $response = $kernel->handle($request);
        $kernel->terminate($request, $response);

        return $response;
    }

    /**
     * 格式化 QueryPHP 响应到 Swoole 响应.
     */
    protected function normalizeResponse(Response $response, SwooleHttpResponse $swooleResponse): SwooleHttpResponse
    {
        $leevel2swoole = new Leevel2Swoole();

        return $leevel2swoole->createResponse($response, $swooleResponse);
    }

    /**
     * 格式化 Swoole 请求到 QueryPHP 请求.
     */
    protected function normalizeRequest(SwooleHttpRequest $swooleRequest): Request
    {
        $swoole2Leevel = new Swoole2Leevel();

        return $swoole2Leevel->createRequest($swooleRequest);
    }

    /**
     * 创建 Swoole 服务.
     */
    protected function createSwooleServer(): void
    {
        $this->server = new SwooleHttpServer($this->option['host'], (int) $this->option['port']);
        $this->initSwooleServer();
    }
}
