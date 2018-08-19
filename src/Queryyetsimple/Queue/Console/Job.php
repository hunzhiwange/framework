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

namespace Leevel\Queue\Console;

use Exception;
use Leevel\Console\Argument;
use Leevel\Console\Command;
use Leevel\Console\Option;
use Leevel\Option as Options;
use PHPQueue\Base;

/**
 * 导入消息队列配置.
 */
require dirname(__DIR__).'/config.php';

/**
 * 添加新的任务
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.05.11
 *
 * @version 1.0
 */
class Job extends Command
{
    /**
     * 命令名字.
     *
     * @var string
     */
    protected $name = 'queue:job';

    /**
     * 命令行描述.
     *
     * @var string
     */
    protected $description = 'Add a new job on a queue';

    /**
     * 响应命令.
     */
    public function handle()
    {
        $this->line($this->time('Adding job, please wating...'));

        $status = false;

        try {
            // 任务名字
            $payload = [
                'job' => $this->argument('job'),
            ];

            // 附加参数
            $payload['data'] = $this->option('data') ?: [];
            $payload['attempts'] = 1;

            // 注册处理的队列
            $connect = 'Leevel\Queue\Queues\\'.$this->argument('connect');

            if (!class_exists($connect)) {
                $this->error($this->time(sprintf('Connect %s not exits.', $connect)));

                return;
            }

            call_user_func_array([
                $connect,
                'setQueue',
            ], [
                $this->option('queue'),
            ]);

            // 添加任务
            $queue = Base::getQueue($this->argument('connect'));
            $status = Base::addJob($queue, $payload);
        } catch (Exception $e) {
            $this->error(
                $this->time(sprintf("Job add error: %s\n", $e->getMessage()))
            );

            throw $e;
        }

        if ($status) {
            $this->info(
                $this->time(
                    sprintf('%s add succeed.', $this->option('queue').':'.$this->argument('job'))
                )
            );
        } else {
            $this->error(
                $this->time(
                    sprintf('%s add failed.', $this->option('queue').':'.$this->argument('job'))
                )
            );
        }
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
                'job',
                Argument::REQUIRED,
                'The job name to add.',
                null,
            ],
            [
                'connect',
                Argument::OPTIONAL,
                'The name of connect. ',
                Options::get('quque\\default', 'redis'),
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
                'queue',
                null,
                Option::VALUE_OPTIONAL,
                'The queue to listen on.',
                'default',
            ],
            [
                'data',
                null,
                Option::VALUE_OPTIONAL | Option::VALUE_IS_ARRAY,
                'The job json args.',
            ],
        ];
    }
}
