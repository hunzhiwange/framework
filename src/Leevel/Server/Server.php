<?php

declare(strict_types=1);

namespace Leevel\Server;

use Leevel\Di\IContainer;
use Leevel\Event\IDispatch;
use Leevel\Filesystem\Helper\CreateFile;
use Leevel\Server\Process\Process;
use Swoole\Coroutine\Http\Server as SwooleHttpServer;
use Swoole\Coroutine\Server as SwooleServer;
use Swoole\Process\Pool;
use Swoole\Table;

/**
 * 服务基类.
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

    /**
     * 服务.
     */
    protected SwooleServer|SwooleHttpServer|null $server = null;

    /**
     * 工作进程运行状态.
     */
    protected WorkerStatusEnum $workerStatus = WorkerStatusEnum::STOPPED;

    /**
     * 进程统计信息.
     */
    protected ?Table $processStatisticTable = null;

    /**
     * 自定义工作进程名字.
     */
    protected array $customWorkerProcessName = [];

    /**
     * 构造函数.
     */
    public function __construct(IContainer $container, array $config = [], ?IDispatch $dispatch = null)
    {
        $this->container = $container;
        $this->config = array_merge($this->config, $config);
        // @todo 暂时没有用，以后可以接入一些事件
        $this->dispatch = $dispatch;
        // 全局启用协程,启用后部分底层代码将采用协程方式运行
        $this->container->instance(IContainer::ENABLED_COROUTINE, true);
    }

    /**
     * {@inheritDoc}
     *
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
     * {@inheritDoc}
     */
    public function getConfig(): array
    {
        $config = $this->config;
        if (isset($config['server'])) {
            unset($config['server']);
        }

        return $config;
    }

    /**
     * {@inheritDoc}
     *
     * @throw \RuntimeException
     */
    public function getProcessStatisticTable(): Table
    {
        if (!$this->processStatisticTable) {
            throw new \RuntimeException('Process statistic table was not create.');
        }

        return $this->processStatisticTable;
    }

    /**
     * 创建进程管理器.
     */
    protected function createProcessManager(): ProcessManager
    {
        $processManager = new ProcessManager();

        $processManager->setManagerStartEvent(function (Pool $pool): void {
            $this->onManagerStart($pool);
        });

        $processManager->setWorkerStopEvent(function (Pool $pool, int $workerId): void {
            $this->workerStatus = WorkerStatusEnum::STOPPED;
            $this->onWorkerStop($pool, $workerId);
        });

        return $processManager;
    }

    /**
     * 运行自定义进程.
     *
     * @throw \InvalidArgumentException
     */
    protected function runCustomProcess(ProcessManager $processManager): void
    {
        foreach ($this->config['custom_process'] as $customProcess => $customProcessConfig) {
            if (!isset($customProcessConfig['enabled']) || true !== $customProcessConfig['enabled']) {
                continue;
            }

            $processManager->add(function (Pool $pool, int $workerId) use ($customProcess, $customProcessConfig): void {
                /** @var Process $process */
                $process = new $customProcess($customProcessConfig);
                if (!$process instanceof Process) {
                    throw new \InvalidArgumentException(sprintf('Expected an instance of Process, got %s instead.', $process::class));
                }

                try {
                    $this->customWorkerProcessName[$workerId] = $process->getName();
                    $this->onWorkerStart($pool, $workerId);
                    $process->handle($this);
                } catch (\Throwable $e) {
                    $this->message('[WARNING]'.$e->getMessage());
                }
            }, true);
        }
    }

    /**
     * 管理主进程启动成功回调.
     *
     * - 进程创建成功后会将进程ID存放到运行文件中，以便于对该进程进行管理。
     *
     * @throws \InvalidArgumentException
     */
    protected function onManagerStart(Pool $poll): void
    {
        if (!$this->config['pid_path']) {
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
     *  工作进程启动成功回调.
     *
     * - 每个 Worker 进程启动或重启时都会执行.
     */
    protected function onWorkerStart(Pool $poll, int $workerId): void
    {
        // 开启 opcache 重连后需要刷新
        if (\function_exists('opcache_reset')) {
            opcache_reset();
        }

        if (\function_exists('apc_clear_cache')) {
            apc_clear_cache();
        }

        $this->setProcessName($processName = $this->getProcessName($workerId));

        $this->message(sprintf(
            '[%s] Process is started at pid:%s',
            $processName,
            posix_getpid(),
        ));
    }

    /**
     * 工作进程终止成功回调.
     */
    protected function onWorkerStop(Pool $poll, int $workerId): void
    {
        $processName = $this->getProcessName($workerId);
        $this->message(sprintf(
            '[%s] Process is shutdown',
            $processName,
        ));
    }

    /**
     * 进程消息.
     */
    protected function message(string $message): void
    {
        $time = sprintf('[%s]', date('Y-m-d H:i:s'));
        fwrite(STDOUT, $time.' '.$message.PHP_EOL);
    }

    /**
     * 设置进程名称.
     */
    protected function setProcessName(string $name): void
    {
        try {
            cli_set_process_title($name);
        } catch (\Throwable $e) {
            $this->message('[WARNING]'.$e->getMessage());
        }
    }

    /**
     * 获取进程名字.
     */
    protected function getProcessName(int $workerId = -1): string
    {
        $processName = 'WORKER';
        if (-1 === $workerId) {
            $processName = 'MASTER';
        } elseif ($this->isCustomWorker($workerId)) {
            $processName = $this->customWorkerProcessName[$workerId];
        }

        return $this->config['process_name'].'.'.$processName.($workerId > -1 ? ' #'.$workerId : '');
    }

    /**
     * 判断是否为自定义进程.
     *
     * - 比如说代码更新后热重启进程就是自定义进程
     */
    protected function isCustomWorker(int $workerId): bool
    {
        return isset($this->customWorkerProcessName[$workerId]);
    }

    /**
     * 创建进程统计表.
     */
    protected function createProcessStatisticTable(): void
    {
        $this->processStatisticTable = new Table(1024);
        $this->processStatisticTable->column('pid', Table::TYPE_INT);
        $this->processStatisticTable->column('request_count', Table::TYPE_INT);
        $this->processStatisticTable->column('kill_start_time', Table::TYPE_INT);
        $this->processStatisticTable->create();
    }
}
