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

namespace Leevel\Queue\Jobs;

use Leevel\Leevel;
use PHPQueue\Job as PHPQueueJob;

/**
 * 基类 job.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.05.12
 *
 * @version 1.0
 */
abstract class Job extends PHPQueueJob
{
    /**
     * 任务所属的消息队列.
     *
     * @var string
     */
    protected $queue;

    /**
     * 任务是否被删除.
     *
     * @var bool
     */
    protected $deleted = false;

    /**
     * 构造函数.
     *
     * @param array  $data
     * @param string $jobId
     * @param string $queue
     */
    public function __construct($data = null, $jobId = null, $queue = 'default')
    {
        parent::__construct($data, $jobId);

        $this->queue = $queue;
        $this->initialization();
    }

    /**
     * 执行任务
     */
    public function handle()
    {
        list($job, $method) = $this->parseString($this->getName());

        $jobInstance = $this->getJob($job);

        $method = method_exists($jobInstance, $method) ?
            $method :
            ('handle' !== $method && method_exists($jobInstance, 'handle') ?
                'handle' :
                'run');

        $this->dispatch([
            $jobInstance,
            $method,
        ]);
    }

    /**
     * 调用任务的失败方法.
     */
    public function failed()
    {
        list($job, $method) = $this->parseString($this->getName());

        $jobInstance = $this->getJob($job);

        if ($jobInstance && method_exists($jobInstance, 'failed')) {
            $this->dispatch([
                $jobInstance,
                'failed',
            ]);
        }
    }

    /**
     * 标识任务删除.
     */
    public function delete()
    {
        $this->deleted = true;
    }

    /**
     * 任务是否被删除.
     *
     * @return bool
     */
    public function isDeleted()
    {
        return $this->deleted;
    }

    /**
     * 取得 job 名字.
     *
     * @return string
     */
    public function getName()
    {
        return $this->data['job'];
    }

    /**
     * 取得 job 数据.
     *
     * @return string
     */
    public function getData()
    {
        return $this->data['data'];
    }

    /**
     * 返回任务执行次数.
     *
     * @return int
     */
    public function getAttempts()
    {
        return $this->data['attempts'];
    }

    /**
     * 获取任务所属的消息队列.
     *
     * @return string
     */
    public function getQueue()
    {
        return $this->queue;
    }

    /**
     * 取得 worker.
     *
     * @return string
     */
    public function getWorker()
    {
        return $this->worker;
    }

    /**
     * 取得 job_id.
     *
     * @return string
     */
    public function getJobId()
    {
        return $this->job_id;
    }

    /**
     * 分析任务名字.
     *
     * @param string $job
     *
     * @return array
     */
    protected function parseString($job)
    {
        $job = explode('@', $job);

        return !empty($job[1]) ? $job : [
            $job[0],
            'handle',
        ];
    }

    /**
     * 取得任务实例.
     *
     * @param string $job
     *
     * @return object
     */
    protected function getJob($job)
    {
        return $this->container()->make($job);
    }

    /**
     * 调度回调方法.
     *
     * @param callable $calFunc
     */
    protected function dispatch($calFunc)
    {
        $this->container()->call($calFunc, $this->args());
    }

    /**
     * 返回服务容器.
     *
     * @return \Leevel\Di\IContainer
     */
    protected function container()
    {
        return Leevel::singletons();
    }

    /**
     * 获取任务调度参数.
     *
     * @return array
     */
    protected function args(): array
    {
        $args = $this->getData();

        array_unshift($args, $this);

        return $args;
    }

    /**
     * 初始化.
     */
    protected function initialization()
    {
        if (!isset($this->data['data'])) {
            $this->data['data'] = [];
        }

        if (!isset($this->data['attempts'])) {
            $this->data['attempts'] = 1;
        }
    }
}
