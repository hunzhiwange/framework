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
namespace Queryyetsimple\Queue\Console;

use Exception;
use PHPQueue\Base;
use Queryyetsimple\Console\{
    Option,
    Command,
    Argument
};

/**
 * 导入消息队列配置
 */
require dirname(__DIR__) . '/config.php';

/**
 * 添加新的任务
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.05.11
 * @version 1.0
 */
class Job extends Command
{

    /**
     * 命令名字
     *
     * @var string
     */
    protected $strName = 'queue:job';

    /**
     * 命令行描述
     *
     * @var string
     */
    protected $strDescription = 'Add a new job on a queue';

    /**
     * 响应命令
     *
     * @return void
     */
    public function handle()
    {
        $this->line($this->time('Adding job, please wating...'));

        $booStatus = false;
        try {
            // 任务名字
            $arrPayload = [
                'job' => $this->argument('job')
            ];

            // 附加参数
            $arrPayload['data'] = $this->option('data') ?  : [];
            $arrPayload['attempts'] = 1;

            // 注册处理的队列
            $strConnect = 'Queryyetsimple\Queue\queues\\' . $this->argument('connect');
            if (! class_exists($strConnect)) {
                $this->error($this->time(sprintf('Connect %s not exits.', $strConnect)));
                return;
            }
            call_user_func_array([
                $strConnect,
                'setQueue'
            ], [
                $this->option('queue')
            ]);

            // 添加任务
            $objQueue = Base::getQueue($this->argument('connect'));
            $booStatus = Base::addJob($objQueue, $arrPayload);
        } catch (Exception $oE) {
            $this->error($this->time(sprintf("Job add error: %s\n", $oE->getMessage())));
            throw $oE;
        }

        if ($booStatus) {
            $this->info($this->time(sprintf("%s add succeed.", $this->option('queue') . ':' . $this->argument('job'))));
        } else {
            $this->error($this->time(sprintf("%s add failed.", $this->option('queue') . ':' . $this->argument('job'))));
        }
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
                'job',
                Argument::REQUIRED,
                'The job name to add.',
                null
            ],
            [
                'connect',
                Argument::OPTIONAL,
                'The name of connect. ',
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
                'The queue to listen on.',
                'default'
            ],
            [
                'data',
                null,
                option::VALUE_OPTIONAL | option::VALUE_IS_ARRAY,
                'The job json args.'
            ]
        ];
    }
}
