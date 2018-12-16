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

namespace Leevel\Protocol\Console;

use InvalidArgumentException;
use Leevel\Console\Argument;
use Leevel\Console\Command;
use Leevel\Leevel;
use Leevel\Option\IOption;
use Swoole\Process;

/**
 * swoole 服务停止.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.12.26
 *
 * @version 1.0
 */
class Stop extends Command
{
    /**
     * 命令名字.
     *
     * @var string
     */
    protected $name = 'swoole:stop';

    /**
     * 命令行描述.
     *
     * @var string
     */
    protected $description = 'Stop swoole service process';

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

        $server = Leevel::make('swoole.'.$this->argument('type').'.server');
        $this->close($server->getOption());
    }

    /**
     * 停止 Swoole 服务.
     *
     * @param array $option
     */
    protected function close(array $option): void
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

        Process::kill($pid, SIGKILL);

        if (is_file($pidFile)) {
            unlink($pidFile);
        }

        $this->info(sprintf('Process %s:%d has stoped.', $processName, $pid), true);
    }

    /**
     * 返回 QueryPHP Version.
     *
     * @return string
     */
    protected function getVersion()
    {
        return 'The Stop Of Swoole '.
            ucfirst($this->argument('type')).
            ' Server Version '.Leevel::version().
            PHP_EOL;
    }

    /**
     * 命令参数.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            [
                'type',
                Argument::OPTIONAL,
                'The type of server,support default,http,websocket.',
                $this->option->get('swoole\\default'),
            ],
        ];
    }

    /**
     * 命令配置.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [];
    }
}
