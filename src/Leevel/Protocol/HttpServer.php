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

namespace Leevel\Protocol;

use Leevel\Http\IRequest;
use Leevel\Http\IResponse;
use Leevel\Kernel\IKernel;
use Swoole\Http\Request as SwooleHttpRequest;
use Swoole\Http\Response as SwooleHttpResponse;
use Swoole\Http\Server as SwooleHttpServer;

/**
 *  HTTP 服务.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.12.25
 *
 * @version 1.0
 * @codeCoverageIgnore
 */
class HttpServer extends Server implements IServer
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
        // 配合 Nginx 可以设置这里为 false,nginx 设置规则解析静态路径动态路由转发给 Swoole
        'enable_static_handler' => false,

        // 开启静态路径目录
        'document_root' => '',

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
        'receive',
        'task',
        'finish',
        'shutdown',
        'close',
    ];

    /**
     * 处理 http 请求.
     *
     * - 浏览器连接服务器后, 页面上的每个请求均会执行一次
     * - nginx 反向代理每次打开链接页面默认都是接收两个请求, 一个是正常的数据请求, 一个 favicon.ico 的请求
     * - 可以通过 nginx deny 屏蔽掉 favicon.ico 的请求，具体请 Google 或者百度.
     *
     * @param \Swoole\Http\Request  $swooleRequest
     * @param \Swoole\Http\Response $swooleResponse
     */
    public function onRequest(SwooleHttpRequest $swooleRequest, SwooleHttpResponse $swooleResponse): void
    {
        if ($this->isFavicon($swooleRequest)) {
            $swooleResponse->end();

            return;
        }

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
     * @param \Swoole\Http\Server $server
     * @param int                 $fd
     * @param int                 $reactorId
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
     *
     * @param \Swoole\Http\Request $swooleRequest
     *
     * @return bool
     */
    protected function isFavicon(SwooleHttpRequest $swooleRequest): bool
    {
        return '/favicon.ico' === $swooleRequest->server['path_info'] ||
            '/favicon.ico' === $swooleRequest->server['request_uri'];
    }

    /**
     * 路由调度.
     *
     * @param \Leevel\Http\IRequest $request
     *
     * @return \Leevel\Http\IResource
     */
    protected function dispatchRouter(IRequest $request): IResponse
    {
        $kernel = $this->container->make(IKernel::class);
        $response = $kernel->handle($request);
        $kernel->terminate($request, $response);
        $this->removeCoroutine();

        return $response;
    }

    /**
     * 格式化 QueryPHP 响应到 Swoole 响应.
     *
     * @param \Leevel\Http\IResponse $response
     * @param \Swoole\Http\Response  $swooleResponse
     *
     * @return \Swoole\Http\Response
     */
    protected function normalizeResponse(IResponse $response, SwooleHttpResponse $swooleResponse): SwooleHttpResponse
    {
        $leevel2swoole = new Leevel2Swoole();
        $response = $leevel2swoole->createResponse($response, $swooleResponse);

        return $response;
    }

    /**
     * 格式化 Swoole 请求到 QueryPHP 请求.
     *
     * @param \Swoole\Http\Request $swooleRequest
     *
     * @return \Leevel\Http\IRequest
     */
    protected function normalizeRequest(SwooleHttpRequest $swooleRequest): IRequest
    {
        $swoole2Leevel = new Swoole2Leevel();
        $request = $swoole2Leevel->createRequest($swooleRequest);

        return $request;
    }

    /**
     * 创建 HTTP Server.
     */
    protected function createServer(): void
    {
        $this->server = new SwooleHttpServer(
            $this->option['host'],
            (int) ($this->option['port'])
        );

        $this->initServer();
    }
}
