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

namespace Leevel\Protocol\Console\Base;

use InvalidArgumentException;
use Leevel\Console\Command;
use Leevel\Console\Option;
use Leevel\Option\IOption;
use Leevel\Protocol\IServer;
use Swoole\Process;

/**
 * swoole 服务重启.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.12.27
 *
 * @version 1.0
 */
abstract class Reload extends Command
{
    /**
     * 配置.
     *
     * @var \Leevel\Option\IOption
     */
    protected $option;

    /**
     * 构造函数.
     *
     * @param \Leevel\Option\IOption $option
     */
    public function __construct(IOption $option)
    {
        $this->option = $option;

        parent::__construct();
    }

    /**
     * 响应命令.
     */
    public function handle()
    {
        $this->warn($this->getVersion());

        $server = $this->createServer();

        $this->reload($server->getOption());
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
     * 重启 Swoole 服务.
     *
     * @param array $option
     */
    protected function reload(array $option): void
    {
        $pidFile = $option['pid_path'];
        $processName = $option['process_name'];

        if (!file_exists($pidFile)) {
            throw new InvalidArgumentException(
                sprintf('Pid path `%s` was not found.', $pidFile)
            );
        }

        $pids = explode(PHP_EOL, file_get_contents($pidFile));
        $pid = (int) $pids[0];

        if (!Process::kill($pid, 0)) {
            throw new InvalidArgumentException(
                sprintf('Pid `%s` was not found.', $pid)
            );
        }

        Process::kill($pid, $this->option('all') ? SIGUSR1 : SIGUSR2);

        // 开启 opcache 重连后需要刷新
        if (function_exists('opcache_reset')) {
            opcache_reset();
        }

        if (function_exists('apc_clear_cache')) {
            apc_clear_cache();
        }

        $this->info(sprintf('Process %s:%d has reloaded.', $processName, $pid), true);
    }

    /**
     * 命令参数.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [];
    }

    /**
     * 命令配置.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            [
                'all',
                null,
                Option::VALUE_OPTIONAL,
                'Reload all progress or only task process.',
            ],
        ];
    }
}
