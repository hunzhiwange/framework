<?php

declare(strict_types=1);

namespace Leevel\Server\Console\Base;

use Leevel\Console\Command;
use Symfony\Component\Console\Input\InputOption;

/**
 * HTTP 服务状态.
 */
abstract class Status extends Command
{
    use Base;

    /**
     * 响应命令.
     */
    public function handle(): int
    {
        $this->showBaseInfo();
        $config = $this->getServerConfig();
        $this->status((string) ($config['process_name'] ?? ''));

        return self::SUCCESS;
    }

    /**
     * 获取 Swoole 服务状态.
     *
     * - $1 USER: 进程的用户
     * - $2 PID: 进程的 ID
     * - $3 %CPU: 进程占用的 CPU 百分比
     * - $4 %MEM: 占用内存的百分比
     * - $5 VSZ(kb): 该进程使用的虚拟内存量（KB）
     * - $6 RSS(kb): 该进程占用的固定内存量（KB）
     * - $7 TTY: 该进程在哪个终端上运行
     * - $8 STAT: STAT 状态
     * - $9 START: 该进程被触发启动时间
     * - $10 TIME: 该进程实际使用CPU运行的时间
     * - $11 COMMAND: 命令的名称和参数
     */
    protected function status(string $processName): void
    {
        if ($this->getOption('all')) {
            $item = '$1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11';
            $nikename = [
                'USER', 'PID', '%CPU', '%MEM', 'VSZ(kb)',
                'TTY', 'STAT', 'START', 'TIME', 'COMMAND',
            ];
        } else {
            $item = '$1, $2, $3, $4, $8, $9, $10, $11';
            $nikename = [
                'USER', 'PID', '%CPU', '%MEM', 'STAT',
                'START', 'TIME', 'COMMAND',
            ];
        }

        $cmd = 'ps aux|grep '.$processName."|grep -v grep|awk '{print ".$item."}'";
        exec($cmd, $out);
        $this->info($cmd);
        if (empty($out)) {
            $this->warn('No swoole server process was found');

            return;
        }

        foreach ($out as &$v) {
            $v = explode(' ', $v);
        }

        $this->table($nikename, $out);
    }

    /**
     * 命令配置.
     */
    protected function getOptions(): array
    {
        return [
            [
                'all',
                'a',
                InputOption::VALUE_NONE,
                'Show all item of process.',
            ],
        ];
    }
}
