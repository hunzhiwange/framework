<?php

declare(strict_types=1);

namespace Leevel\Server;

use Leevel\Di\IContainer;
use Leevel\Event\IDispatch;
use Leevel\Filesystem\Helper\CreateFile;
use Swoole\Coroutine\Http\Server as SwooleHttpServer;
use Swoole\Coroutine\Server as SwooleServer;
use Swoole\Process\Pool;

/**
 * Swoole 服务基类.
 */
abstract class Server implements IServer
{
    /**
     * 配置.
     */
    protected array $config = [];

    /**
     * IOC 容器.
     */
    protected IContainer $container;

    /**
     * 事件处理器.
     */
    protected ?IDispatch $dispatch = null;

    protected SwooleServer|SwooleHttpServer|null $server = null;

    /**
     * 构造函数.
     */
    public function __construct(IContainer $container, array $config = [], ?IDispatch $dispatch = null)
    {
        //  $container->remove('request');
        // $container->setCoroutine($coroutine);
        $this->container = $container;
        $this->config = array_merge($this->config, $config);
        $this->dispatch = $dispatch;
        $this->container->instance(IContainer::ENABLED_COROUTINE, true);
    }

    public function getConfig(): array
    {
        $config = $this->config;
        foreach (['server'] as $key) {
            if (isset($config[$key])) {
                unset($config[$key]);
            }
        }

        return $config;
    }

    /**
     * @throws \RuntimeException
     */
    public function getServer(): SwooleServer|SwooleHttpServer
    {
        if (!$this->server) {
            throw new \RuntimeException('Server was not started.');
        }

        return $this->server;
    }

    /**
     *  工作进程启动.
     *
     * - 每个 Worker 进程启动或重启时都会执行.
     */
    public function workerStart(Pool $pool, int $workeId): void
    {
        // 开启 opcache 重连后需要刷新
        if (\function_exists('opcache_reset')) {
            opcache_reset();
        }

        if (\function_exists('apc_clear_cache')) {
            apc_clear_cache();
        }

        $this->setProcessName($processName = $this->getProcessName($workeId));

        $this->message(sprintf(
            '[%s] Process is started at pid:%s',
            $processName,
            posix_getpid(),
        ));
    }

    /**
     * 工作进程终止时发生
     */
    public function workerStop(Pool $pool, int $workerId): void
    {
        $processName = $this->getProcessName($workerId);
        $this->message(sprintf(
            '[%s] Process is shutdown',
            $processName,
        ));
    }

    protected function isCustomWorker(int $workerId): bool
    {
        return $workerId >= $this->config['worker_num'];
    }

    /**
     * @throws \InvalidArgumentException
     */
    protected function managerStart(): void
    {
        if (!$pidPath = $this->config['pid_path']) {
            throw new \InvalidArgumentException('Pid path is not set');
        }

        $this->setProcessName($processName = $this->getProcessName());

        $this->message(sprintf(
            'Server is started at %s:%d',
            $this->config['host'],
            $this->config['port'],
        ));

        $this->message(sprintf(
            '[%s] Process is started at pid:%s',
            $processName,
            posix_getpid(),
        ));

        CreateFile::handle($this->config['pid_path'], (string) getmypid());
    }

    /**
     * 设置 swoole 进程名称.
     *
     * @throws \InvalidArgumentException
     */
    protected function setProcessName(string $name): void
    {
        try {
            if (\function_exists('cli_set_process_title')) {
                cli_set_process_title($name);
            } else {
                if (\function_exists('swoole_set_process_name')) {
                    swoole_set_process_name($name);
                } else {
                    throw new \InvalidArgumentException('Require cli_set_process_title or swoole_set_process_name.');
                }
            }
        } catch (\Throwable $e) {
            $this->message('[WARNING]'.$e->getMessage());
        }
    }

    protected function message(string $message): void
    {
        $time = sprintf('[%s]', date('Y-m-d H:i:s'));
        fwrite(STDOUT, $time.' '.$message.PHP_EOL);
    }

    protected function getProcessName(int $workeId = -1): string
    {
        $processName = 'WORKER';
        if (-1 === $workeId) {
            $processName = 'MASTER';
        } elseif ($this->isCustomWorker($workeId)) {
            $processName = 'CUSTOM';
        }

        return $this->config['process_name'].'.'.$processName.($workeId > -1 ? ' #'.$workeId : '');
    }
}
