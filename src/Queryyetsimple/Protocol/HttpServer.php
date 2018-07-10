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

use Leevel\Http\IResponse;
use Leevel\Http\RedirectResponse;
use Leevel\Http\Request;
use Leevel\Kernel\IKernel;
use Swoole\Http\Request as SwooleHttpRequest;
use Swoole\Http\Response as SwooleHttpResponse;
use Swoole\Http\Server as SwooleHttpServer;

/**
 * swoole http 服务
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.12.25
 *
 * @version 1.0
 */
class HttpServer extends Server
{
    /**
     * 内核.
     *
     * @var \Leevel\Kernel\IKernel
     */
    protected $kernel;

    /**
     * 请求.
     *
     * @var \Leevel\Http\Request
     */
    protected $request;

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
        'process_name' => 'queryphp.swoole.http',

        // swoole 进程保存路径
        'pid_path' => '',

        // 设置启动的 worker 进程数
        // see https://wiki.swoole.com/wiki/page/275.html
        'worker_num' => 8,

        // 守护进程化
        // see https://wiki.swoole.com/wiki/page/278.html
        'daemonize' => 0,
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
        'close',
    ];

    /**
     * 构造函数.
     *
     * @param \Leevel\Kernel\IKernel $kernel
     * @param \Leevel\Http\Request   $request
     * @param array                  $option
     */
    public function __construct(IKernel $kernel, Request $request, array $option = [])
    {
        parent::__construct($option);

        $this->kernel = $kernel;
        $this->request = $request;
    }

    /**
     * 处理 http 请求
     * 浏览器连接服务器后, 页面上的每个请求均会执行一次
     * nginx 反向代理每次打开链接页面默认都是接收两个请求, 一个是正常的数据请求, 一个 favicon.ico 的请求
     * 可以通过 nginx deny 屏蔽掉 favicon.ico 的请求，具体请 Google 或者百度.
     *
     * @param \Swoole\Http\Request  $swooleRequest
     * @param \Swoole\Http\Response $swooleResponse
     */
    public function onRequest(SwooleHttpRequest $swooleRequest, SwooleHttpResponse $swooleResponse): void
    {
        // 请求过滤 favicon
        if ('/favicon.ico' === $swooleRequest->server['path_info'] ||
            '/favicon.ico' === $swooleRequest->server['request_uri']) {
            $swooleResponse->end(' ');

            return;
        }

        $request = $this->normalizeRequest($swooleRequest);

        $response = $this->kernel->handle($request);

        $swooleResponse = $this->normalizeResponse($response, $swooleResponse);

        $this->kernel->terminate($request, $response);

        $swooleResponse->end();
    }

    /**
     * 格式化 QueryPHP 响应到 swoole 响应.
     *
     * @param \Leevel\Http\IResponse $response
     * @param \Swoole\Http\Response  $swooleResponse
     *
     * @return \Swoole\Http\Response
     */
    protected function normalizeResponse(IResponse $response, SwooleHttpResponse $swooleResponse): SwooleHttpResponse
    {
        foreach ($response->getCookies() as $item) {
            call_user_func_array([$swooleResponse, 'cookie'], $item);
        }

        if ($response instanceof RedirectResponse &&
            method_exists($swooleResponse, 'redirect')) {
            $swooleResponse->redirect($response->getTargetUrl());
        }

        foreach ($response->headers->all() as $key => $value) {
            $swooleResponse->header($key, $value);
        }

        $swooleResponse->status($response->getStatusCode());

        $swooleResponse->write($response->getContent() ?: ' ');

        return $swooleResponse;
    }

    /**
     * 格式化 swoole 请求到 QueryPHP 请求
     *
     * @param \Swoole\Http\Request $swooleRequest
     *
     * @return \Leevel\Http\Request
     */
    protected function normalizeRequest(SwooleHttpRequest $swooleRequest): Request
    {
        $this->request->reset();

        $datas = [
            'header' => 'headers',
            'server' => 'server',
            'cookie' => 'cookies',
            'get'    => 'query',
            'files'  => 'files',
            'post'   => 'request',
        ];

        $servers = [];

        if ($swooleRequest->header) {
            $tmp = $tmpHeader = [];

            foreach ($swooleRequest->header as $key => $value) {
                $key = strtoupper(str_replace('-', '_', $key));
                $tmpHeader[$key] = $value;

                $key = 'HTTP_'.$key;
                $tmp[$key] = $value;
            }

            $servers = $tmp;
            $swooleRequest->header = $tmpHeader;
        }

        if ($swooleRequest->server) {
            $swooleRequest->server = array_change_key_case(
                $swooleRequest->server,
                CASE_UPPER
            );

            $servers = array_merge($servers, $swooleRequest->server);
            $swooleRequest->server = $servers;
        } else {
            $swooleRequest->server = $servers ?: null;
        }

        foreach ($datas as $key => $item) {
            if ($swooleRequest->{$key}) {
                $this->request->{$item}->replace($swooleRequest->{$key});
            }
        }

        return $this->request;
    }

    /**
     * 创建 http server.
     */
    protected function createServer()
    {
        unset($this->option['task_worker_num']);

        $this->server = new SwooleHttpServer(
            $this->option['host'],
            (int) ($this->option['port'])
        );

        $this->initServer();
    }
}
