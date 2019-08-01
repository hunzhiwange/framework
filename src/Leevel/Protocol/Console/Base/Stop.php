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

use InvalidArgumentException;
use Leevel\Console\Command;
use Leevel\Protocol\IServer;
use Swoole\Process;

/**
 * Swoole 服务停止.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.12.26
 *
 * @version 1.0
 * @codeCoverageIgnore
 */
abstract class Stop extends Command
{
    /**
     * 响应命令.
     */
    public function handle(): void
    {
        $this->info($this->getLogo());
        $this->warn($this->getVersion());
        $server = $this->createServer();
        $this->close($server->getOption());
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
     * 停止 Swoole 服务.
     *
     * @param array $option
     *
     * @throws \InvalidArgumentException
     */
    protected function close(array $option): void
    {
        $pidFile = $option['pid_path'];
        $processName = $option['process_name'];

        if (!file_exists($pidFile)) {
            $e = sprintf('Pid path `%s` was not found.', $pidFile);

            throw new InvalidArgumentException($e);
        }

        $pids = explode(PHP_EOL, file_get_contents($pidFile));
        $pid = (int) $pids[0];

        if (!Process::kill($pid, 0)) {
            $e = sprintf('Pid `%s` was not found.', $pid);

            throw new InvalidArgumentException($e);
        }

        Process::kill($pid, SIGKILL);

        if (is_file($pidFile)) {
            unlink($pidFile);
        }

        $message = sprintf('Process %s:%d has stoped.', $processName, $pid);
        $this->info($message);
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
        return [];
    }
}
