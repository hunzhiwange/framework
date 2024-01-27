<?php

declare(strict_types=1);

namespace Leevel\Server;

use Leevel\Http\Request;
use Leevel\Kernel\IKernel;
use Leevel\Server\Process\HotOverload;
use Swoole\Coroutine;
use Swoole\Coroutine\Http\Server as SwooleHttpServer;
use Swoole\Coroutine\Server as SwooleServer;
use Swoole\Http\Request as SwooleHttpRequest;
use Swoole\Http\Response as SwooleHttpResponse;
use Swoole\Process;
use Swoole\Process\Pool;
use Swoole\Table;
use Swoole\Timer;
use Swoole\WebSocket\CloseFrame;
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
        'port' => '9501',

        // 是否启用 SSL/TLS 隧道加密
        // https://wiki.swoole.com/#/coroutine/http_server
        'ssl' => false,

        // Swoole 进程名称
        'process_name' => 'server.http',

        // Swoole 进程保存路径
        'pid_path' => '',

        // 设置启动的 worker 进程数
        'worker_num' => 8,

        // 开启静态路径
        // 配合 Nginx 可以设置这里为 false,Nginx 设置规则解析静态路径动态路由转发给 Swoole
        'enable_static_handler' => false,

        // 开启静态路径目录
        'document_root' => '',

        // 自定义进程
        'processes' => [],

        // 设置 worker 进程的最大任务数。【默认值：0 即不会退出进程】
        'max_request' => 0,

        'max_wait_time' => 10,

        // 系统配置
        'settings' => [
        ],
    ];

    protected StatusEnum $status = StatusEnum::UNKNOWN;

    protected ?Table $statisticTable = null;

    protected array $connections = [];

    public function start(): void
    {
        $this->createStatisticTable();

        $processManager = new ProcessManager();
        $processManager->setManagerStartEvent(function (Pool $pool): void {
            $this->managerStart();
        });
        $processManager->setWorkerStartEvent(function (Pool $pool, int $workerId): void {
            $this->status = StatusEnum::OK;
            $this->workerStart($pool, $workerId);
        });
        $processManager->setWorkerStopEvent(function (Pool $pool, int $workerId): void {
            $this->status = StatusEnum::STOPPED;
            $this->workerStop($pool, $workerId);
        });
        $processManager->addBatch($this->config['worker_num'], function (Pool $pool, int $workerId): void {
            // 初始化 HTTP 进程信息
            $this->statisticTable->set((string) $workerId, [
                'pid' => posix_getpid(),
                'request_count' => 0,
                'kill_start_time' => 0,
            ]);

            $this->server = $server = new SwooleHttpServer($this->config['host'], $this->config['port'], $this->config['ssl'], true);
            $server->set(array_replace($server->settings ?? [], (array) ($this->config['settings'] ?? [])));

            Process::signal(SIGTERM, function () use ($server): void {
                $this->serverShutdown($server);
            });

            if (Websocket::class === static::class) {
                $server->handle('/websocket', function (SwooleHttpRequest $request, SwooleHttpResponse $response) use ($workerId): void {
                    $this->statisticTable->incr((string) $workerId, 'request_count');
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
            } else {
                $server->handle('/', function (SwooleHttpRequest $request, SwooleHttpResponse $response) use ($workerId): void {
                    $this->statisticTable->incr((string) $workerId, 'request_count');
                    Coroutine::create(function () use ($request, $response): void {
                        $this->onRequest($request, $response);
                    });
                });
            }

            $server->start();
        }, true);

        $processManager->add(function (Pool $pool, int $workerId): void {
            $process = new HotOverload(
                (array) $this->config['hotoverload_watch'],
                (int) $this->config['hotoverload_delay_count'],
                (int) $this->config['hotoverload_time_interval'],
            );
            $process->handle(function (): void {
                // 开发阶段,热重启立刻强制杀掉所有进程,全部重启拉起新的进程
                foreach ($this->statisticTable as $workerId => $v) {
                    Process::kill($v['pid'], SIGKILL);
                }
            });
        }, true);
        $processManager->add(function (Pool $pool, int $workerId): void {
            Timer::tick(3000, function (int $timerId): void {
                foreach ($this->statisticTable as $workerId => $v) {
                    if ($v['request_count'] >= $this->config['max_request']) {
                        if ($v['kill_start_time'] > 0) {
                            if ($this->config['max_wait_time'] > 0
                                && time() - $v['kill_start_time'] >= $this->config['max_wait_time']) {
                                Process::kill($v['pid'], SIGKILL);
                            }
                        } else {
                            $this->statisticTable->set($workerId, ['kill_start_time' => time()]);
                            Process::kill($v['pid'], SIGTERM);
                        }
                    }
                }
            });
        }, true);

        $processManager->start();
    }

    public function getConnections(): array
    {
        return $this->connections;
    }

    protected function close(SwooleHttpRequest $swooleRequest, SwooleHttpResponse $swooleResponse, int $workerId, string|false|Frame|CloseFrame $frame): void
    {
        $this->onClose($swooleRequest, $swooleResponse, $workerId, $frame);
        unset($this->connections[$swooleRequest->fd]);
        $swooleResponse->close();
    }

    protected function onHandShake(SwooleHttpRequest $swooleRequest, SwooleHttpResponse $swooleResponse, int $workerId): void
    {
        if (!$swooleResponse->upgrade()) {
            return;
        }

        $this->connections[spl_object_id($swooleResponse)] = $swooleResponse;
        $response = $this->onOpen($swooleRequest, $swooleResponse, $workerId);
        $swooleResponse->push((string) $response);

        while (true) {
            $frame = $swooleResponse->recv(-1);
            if ('' === $frame) {
                $this->close($swooleRequest, $swooleResponse, $workerId, $frame);

                break;
            }
            if (false === $frame) {
                $this->close($swooleRequest, $swooleResponse, $workerId, $frame);

                break;
            }
            if ('close' === $frame->data || CloseFrame::class === $frame::class) {
                $this->close($swooleRequest, $swooleResponse, $workerId, $frame);

                break;
            }

            $this->onMessage($swooleRequest, $swooleResponse, $workerId, $frame);
        }
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
        return (new TransformResponse())->createResponse($response, $swooleResponse);
    }

    protected function normalizeRequest(SwooleHttpRequest $swooleRequest): Request
    {
        return (new TransformRequest())->createRequest($swooleRequest);
    }

    protected function serverShutdown(SwooleServer|SwooleHttpServer $server): void
    {
        if (StatusEnum::OK !== $this->status) {
            return;
        }

        $this->status = StatusEnum::STOPPING;
        $server->shutdown();
    }

    protected function createStatisticTable(): void
    {
        $this->statisticTable = new Table(1024);
        $this->statisticTable->column('pid', Table::TYPE_INT);
        $this->statisticTable->column('request_count', Table::TYPE_INT);
        $this->statisticTable->column('kill_start_time', Table::TYPE_INT);
        $this->statisticTable->create();
    }
}
