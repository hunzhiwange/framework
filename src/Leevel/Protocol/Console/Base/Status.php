<?php

declare(strict_types=1);

namespace Leevel\Protocol\Console\Base;

use Leevel\Console\Command;
use Leevel\Protocol\IServer;
use Symfony\Component\Console\Input\InputOption;

/**
 * Swoole 服务列表.
 */
abstract class Status extends Command
{
    /**
     * 响应命令.
     */
    public function handle(): int
    {
        $this->info($this->getLogo());
        $this->warn($this->getVersion());
        $server = $this->createServer();
        $this->status((string) $server->option['process_name']);

        return 0;
    }

    /**
     * 创建 server.
     */
    abstract protected function createServer(): IServer;

    /**
     * 返回 Version.
     */
    abstract protected function getVersion(): string;

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
        if (true === $this->getOption('all')) {
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
            $this->warn('No swoole service process was found');

            return;
        }

        foreach ($out as &$v) {
            $v = explode(' ', $v);
        }

        $this->table($nikename, $out);
    }

    /**
     * 返回 QueryPHP Logo.
     */
    protected function getLogo(): string
    {
        return <<<'queryphp'
            _____________                           _______________
             ______/     \__  _____  ____  ______  / /_  _________
              ____/ __   / / / / _ \/ __`\/ / __ \/ __ \/ __ \___
               __/ / /  / /_/ /  __/ /  \  / /_/ / / / / /_/ /__
                 \_\ \_/\____/\___/_/   / / .___/_/ /_/ .___/
                    \_\                /_/_/         /_/
            queryphp;
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
