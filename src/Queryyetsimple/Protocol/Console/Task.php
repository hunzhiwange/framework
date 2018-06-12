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

use Leevel\Console\Option;
use Leevel\Console\Command;
use Leevel\Console\Argument;

/**
 * swoole 服务任务
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.12.29
 *
 * @version 1.0
 */
class Task extends Command
{
    /**
     * 命令名字.
     *
     * @var string
     */
    protected $name = 'swoole:task';

    /**
     * 命令行描述.
     *
     * @var string
     */
    protected $description = 'Start a task on swoole service process';

    /**
     * 响应命令.
     */
    public function handle()
    {
        $this->warn($this->getVersion());

        $server = app('swoole.' . $this->argument('type') . '.server');
        $server->setCommand($this);
        $server->options($this->parseOption());
        $server->taskServer();
    }

    /**
     * 分析参数.
     *
     * @return array
     */
    protected function parseOption(): array
    {
        $option = [];

        foreach (['host', 'port', 'pid_path'] as $key) {
            if (null !== $this->option($key)) {
                $option[$key] = $this->option($key);
            }
        }

        return $option;
    }

    /**
     * 返回 QueryPHP Version.
     *
     * @return string
     */
    protected function getVersion()
    {
        return 'The Stop of Swoole ' .
            ucfirst($this->argument('type')) .
            ' Server Version ' . app()->version() .
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
                app('option')['swoole\default'],
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
        return [
            [
                'host',
                null,
                Option::VALUE_OPTIONAL,
                'The host to listen on',
            ],
            [
                'port',
                null,
                Option::VALUE_OPTIONAL,
                'The port to listen on',
            ],
            [
                'pid_path',
                null,
                Option::VALUE_OPTIONAL,
                'The save path of process',
            ],
        ];
    }
}
