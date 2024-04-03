<?php

declare(strict_types=1);

namespace Leevel\Server\Process;

use Leevel\Server\IServer;
use Leevel\Support\Str\Camelize;
use Swoole\Process as SwooleProcess;
use Swoole\Timer;

/**
 * 最大请求.
 */
class MaxRequest extends Process
{
    /**
     * 进程名字.
     */
    protected string $name = 'MAX.REQUEST';

    /**
     * 最大等待时.
     *
     * - 设置 worker 进程的最大任务数
     * - 默认值：0 即不会退出进程
     * - https://wiki.swoole.com/#/server/setting?id=max_request
     */
    protected int $maxRequest = 0;

    /**
     * 最大等待时.
     *
     * - 设置 Worker 进程收到停止服务通知后最大等待时间【默认值：3】
     * - https://wiki.swoole.com/#/server/setting?id=max_wait_time
     */
    protected int $maxWaitTime = 3;

    /**
     * 构造函数.
     */
    public function __construct(array $config = [])
    {
        $props = [
            'max_request',
            'max_wait_time',
        ];

        foreach ($props as $k) {
            if (isset($config[$k])) {
                $prop = Camelize::handle($k);
                $this->{$prop} = $config[$k];
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function handle(IServer $server): void
    {
        Timer::tick(1000, function () use ($server): void {
            foreach ($server->getProcessStatisticTable() as $workerId => $v) {
                if (0 === $this->maxRequest || $v['request_count'] < $this->maxRequest) {
                    return;
                }

                if ($v['kill_start_time'] > 0) {
                    if ($this->maxWaitTime > 0
                        && time() - $v['kill_start_time'] >= $this->maxWaitTime) {
                        SwooleProcess::kill($v['pid'], SIGKILL);
                    }
                } else {
                    $server->getProcessStatisticTable()->set($workerId, ['kill_start_time' => time()]);
                    SwooleProcess::kill($v['pid'], SIGTERM);
                }
            }
        });
    }
}
