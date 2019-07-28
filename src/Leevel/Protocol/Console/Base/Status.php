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

namespace Leevel\Protocol\Console\Base;

use Leevel\Console\Command;
use Leevel\Console\Option;
use Leevel\Protocol\IServer;

/**
 * swoole 服务列表.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.12.26
 *
 * @version 1.0
 * @codeCoverageIgnore
 */
abstract class Status extends Command
{
    /**
     * 响应命令.
     */
    public function handle(): void
    {
        $this->info($this->getLogo());
        $this->warn($this->getVersion());
        $server = $this->createServer();
        $this->status($server->getOption());
    }

    /**
     * 创建 server.
     *
     * @return \Leevel\Protocol\IServer
     */
    abstract protected function createServer(): IServer;

    /**
     * 返回 Version.
     *
     * @return string
     */
    abstract protected function getVersion(): string;

    /**
     * 获取 Swoole 服务状态.
     *
     * @param array $option
     */
    protected function status(array $option): void
    {
        $processName = $option['process_name'];

        if (true === $this->option('all')) {
            $item = '$1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11';

            $nikename = [
                // 进程的用户
                'USER',

                // 进程的 ID
                'PID',

                // 进程占用的 CPU 百分比
                '%CPU',

                // 占用内存的百分比
                '%MEM',

                // 该进程使用的虚拟内存量（KB）
                'VSZ(kb)',

                // 该进程占用的固定内存量（KB）
                'RSS(kb)',

                // 该进程在哪个终端上运行
                'TTY',

                // STAT 状态
                'STAT',

                // 该进程被触发启动时间
                'START',

                // 该进程实际使用CPU运行的时间
                'TIME',

                // 命令的名称和参数
                'COMMAND',
            ];
        } else {
            $item = '$1, $2, $3, $4, $8, $9, $10, $11';

            $nikename = [
                // 进程的用户
                'USER',

                // 进程的 ID
                'PID',

                // 进程占用的 CPU 百分比
                '%CPU',

                // 占用内存的百分比
                '%MEM',

                // STAT 状态
                'STAT',

                // 该进程被触发启动时间
                'START',

                // 该进程实际使用CPU运行的时间
                'TIME',

                // 命令的名称和参数
                'COMMAND',
            ];
        }

        $cmd = 'ps aux|grep '.$processName."|grep -v grep|awk '{print ".$item."}'";

        exec($cmd, $out);

        $this->info($cmd, true);

        if (empty($out)) {
            $this->warn('No swoole service process was found', true);

            return;
        }

        foreach ($out as &$v) {
            $v = explode(' ', $v);
        }

        $this->table($nikename, $out);
    }

    /**
     * 返回 QueryPHP Logo.
     *
     * @return string
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
     * 命令参数.
     *
     * @return array
     */
    protected function getArguments(): array
    {
        return [];
    }

    /**
     * 命令配置.
     *
     * @return array
     */
    protected function getOptions(): array
    {
        return [
            [
                'all',
                'a',
                Option::VALUE_NONE,
                'Show all item of process.',
            ],
        ];
    }
}
