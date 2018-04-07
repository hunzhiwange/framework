<?php
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
namespace Leevel\Queue\Console;

use PHPQueue\Base;
use PHPQueue\Runner;
use Leevel\Console\{
    Option,
    Command,
    Argument
};

/**
 * 运行任务
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.05.11
 * @version 1.0
 */
class Work extends Command
{

    /**
     * 命令名字
     *
     * @var string
     */
    protected $strName = 'queue:work';

    /**
     * 命令行描述
     *
     * @var string
     */
    protected $strDescription = 'Process the next job on a queue';

    /**
     * 当前进程启动时间
     *
     * @var int|false
     */
    protected $mixRestart;

    /**
     * 响应命令
     *
     * @return void
     */
    public function handle()
    {
        // 注册处理的队列
        $this->setQueue($this->argument('connect'), $this->option('queue'));

        // 守候进程
        $this->runWorker($this->argument('connect'), $this->option('queue'));
    }

    /**
     * 任务不可用等待时间
     *
     * @return int
     */
    public function sleep()
    {
        return ( int ) $this->option('sleep');
    }

    /**
     * 任务最大尝试次数
     *
     * @return int
     */
    public function tries()
    {
        return ( int ) $this->option('tries');
    }

    /**
     * runner 执行完毕当前任务检测是否需要重启
     * 内存不够也需要重启
     *
     * @return void
     */
    public function checkRestart()
    {
        if ($this->memory()) {
            $this->stop();
        }

        if ($this->shouleRestart($this->mixRestart)) {
            $this->stop();
        }
    }

    /**
     * 停止守候进程
     *
     * @return void
     */
    public function stop()
    {
        $this->error($this->time(sprintf('%s has stoped.', $this->argument('connect') . ':' . $this->option('queue'))));
        die();
    }

    /**
     * 设置消息队列
     *
     * @param string $strConnect
     * @param string $strQueue
     * @return void
     */
    protected function setQueue($strConnect, $strQueue)
    {
        $strConnect = 'Leevel\Queue\queues\\' . $strConnect;
        if (! class_exists($strConnect)) {
            $this->error($this->time(sprintf('connect %s not exits.', $strConnect)));
            return;
        }
        call_user_func_array([
            $strConnect,
            'setQueue'
        ], [
            $strQueue
        ]);
    }

    /**
     * 守候进程
     *
     * @param string $strConnect
     * @param string $strQueue
     * @return void
     */
    protected function runWorker($strConnect, $strQueue)
    {
        // 验证运行器是否存在
        $strRunner = 'Leevel\Queue\runners\\' . $strConnect;
        if (! class_exists($strRunner)) {
            $this->error($this->time(sprintf('runner %s not exits.', $strRunner)));
            return;
        }

        $this->info($this->time(sprintf('%s is on working.', $strConnect . ':' . $strQueue)));

        $this->mixRestart = $this->getRestart();

        // 守候进程
        (new $strRunner())->workCommand($this)->run();
    }

    /**
     * 获取上次重启时间
     *
     * @return int|false
     */
    protected function getRestart()
    {
        return cache('queryphp.queue.restart');
    }

    /**
     * 检查是否要重启守候进程
     *
     * @param int|false $mixRestart
     * @return bool
     */
    protected function shouleRestart($mixRestart)
    {
        return $this->getRestart() != $mixRestart;
    }

    /**
     * 检查内存是否超出
     *
     * @return boolean
     */
    protected function memory()
    {
        return memory_get_usage() / 1024 / 1024 >= $this->option('memory');
    }

    /**
     * 命令参数
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            [
                'connect',
                Argument::OPTIONAL,
                'The name of connection.',
                option('quque\default', 'redis')
            ]
        ];
    }

    /**
     * 命令配置
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            [
                'queue',
                null,
                option::VALUE_OPTIONAL,
                'The queue to listen on',
                'default'
            ],
            [
                'memory',
                null,
                option::VALUE_OPTIONAL,
                'The memory limit in megabytes',
                128
            ],
            [
                'sleep',
                null,
                option::VALUE_OPTIONAL,
                'Number of seconds to sleep when no job is available',
                5
            ],
            [
                'tries',
                null,
                option::VALUE_OPTIONAL,
                'Number of times to attempt a job before logging it failed',
                0
            ]
        ];
    }
}
