<?php

declare(strict_types=1);

namespace Leevel\Server;

use Leevel\Http\Request;
use Leevel\Kernel\IKernel;
use Swoole\Coroutine;
use Swoole\Coroutine\Http\Server as SwooleHttpServer;
use Swoole\Coroutine\Server as SwooleServer;
use Swoole\Http\Request as SwooleHttpRequest;
use Swoole\Http\Response as SwooleHttpResponse;
use Swoole\Process;
use Swoole\Process\Pool;
use Symfony\Component\HttpFoundation\Response;

/**
 *  HTTP 服务.
 */
class Http extends Server implements IServer
{
    /**
     * 配置.
     */
    protected array $config = [
        // 监听 IP 地址
        // https://wiki.swoole.com/#/coroutine/http_server
        'host' => '0.0.0.0',

        // 监听端口
        // https://wiki.swoole.com/#/coroutine/http_server
        'port' => 9527,

        // 是否启用 SSL/TLS 隧道加密
        // https://wiki.swoole.com/#/coroutine/http_server
        'ssl' => false,

        // 进程名称
        'process_name' => 'SERVER.HTTP',

        // 进程保存路径
        'pid_path' => '',

        // 设置启动的 worker 进程数
        'worker_num' => 8,

        // 自定义进程
        'custom_process' => [],

        // 服务配置
        'settings' => [],
    ];

    public function start(): void
    {
        $this->createProcessStatisticTable();
        $processManager = $this->createProcessManager();
        $processManager->addBatch($this->config['worker_num'], function (Pool $pool, int $workerId): void {
            $this->initWorkerProcessStatisticInfo($workerId);
            $this->onWorkerStart($pool, $workerId);
            $this->server = $this->createHttpServer();
            $this->seedServerShutdownSignal($this->server);
            $this->runRequest($this->server, $workerId);
            $this->server->start();
        }, true);

        $this->runCustomProcess($processManager);

        $processManager->start();
    }

    protected function runRequest(SwooleHttpServer $server, int $workerId): void
    {
        $this->onHttpRequest($server, $workerId);
    }

    protected function createHttpServer(): SwooleHttpServer
    {
        $server = new SwooleHttpServer($this->config['host'], $this->config['port'], $this->config['ssl'], true);
        $server->set(array_replace($server->settings ?? [], (array) ($this->config['settings'] ?? [])));

        return $server;
    }

    /**
     * 初始化 HTTP 进程信息.
     */
    protected function initWorkerProcessStatisticInfo(int $workerId): void
    {
        $this->processStatisticTable->set((string) $workerId, [
            'pid' => posix_getpid(),
            'request_count' => 0,
            'kill_start_time' => 0,
        ]);
    }

    protected function seedServerShutdownSignal(SwooleHttpServer $server): void
    {
        Process::signal(SIGTERM, function () use ($server): void {
            $this->serverShutdown($server);
        });
    }

    protected function onHttpRequest(SwooleHttpServer $server, int $workerId): void
    {
        $server->handle('/', function (SwooleHttpRequest $request, SwooleHttpResponse $response) use ($workerId): void {
            $this->processStatisticTable->incr((string) $workerId, 'request_count');
            Coroutine::create(function () use ($request, $response): void {
                $this->onRequest($request, $response);
            });
        });
    }

    /**
     * 处理 HTTP 请求.
     */
    protected function onRequest(SwooleHttpRequest $swooleRequest, SwooleHttpResponse $swooleResponse): void
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
     * 请求过滤 favicon.
     */
    protected function isFavicon(SwooleHttpRequest $swooleRequest): bool
    {
        return '/favicon.ico' === $swooleRequest->server['path_info']
            || '/favicon.ico' === $swooleRequest->server['request_uri'];
    }

    /**
     * 路由调度.
     */
    protected function dispatchRouter(Request $request): Response
    {
        /** @var IKernel $kernel */
        $kernel = $this->container->make(IKernel::class);
        $response = $kernel->handle($request);
        $kernel->terminate($request, $response);

        return $response;
    }

    protected function normalizeResponse(Response $response, SwooleHttpResponse $swooleResponse): SwooleHttpResponse
    {
        return new TransformResponse()->createResponse($response, $swooleResponse);
    }

    protected function normalizeRequest(SwooleHttpRequest $swooleRequest): Request
    {
        return new TransformRequest()->createRequest($swooleRequest);
    }

    protected function serverShutdown(SwooleHttpServer|SwooleServer $server): void
    {
        if (WorkerStatusEnum::OK !== $this->workerStatus) {
            return;
        }

        $this->workerStatus = WorkerStatusEnum::STOPPING;

        // @phpstan-ignore-next-line
        $server->shutdown();
    }
}
