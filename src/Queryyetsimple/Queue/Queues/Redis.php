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

namespace Leevel\Queue\Queues;

use Leevel\Queue\Backend\Redis as BackendRedis;

/**
 * redis 消息队列.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.05.11
 *
 * @version 1.0
 */
class Redis extends Queue implements IQueue
{
    /**
     * 队列连接.
     *
     * @var string
     */
    protected $connect = 'redis';

    /**
     * 连接配置.
     *
     * @var array
     */
    protected $sourceConfig = [
        'servers' => [
            'host' => '127.0.0.1',
            'port' => 6379,
        ],
        'redis_options' => [],
    ];

    /**
     * 队列执行者.
     *
     * @var string
     */
    protected $queueWorker = 'redis';

    /**
     * 构造函数.
     */
    public function __construct()
    {
        parent::__construct();

        $this->sourceConfig['servers'] = $this->getServers();
        $this->sourceConfig['redis_options'] = $this->getOptions();

        $this->resDataSource = new BackendRedis($this->sourceConfig);
    }

    /**
     * 取得消息队列长度.
     *
     * @return int
     */
    public function getQueueSize()
    {
    }

    /**
     * 获取 redis 服务器主机.
     *
     * @return array
     */
    protected function getServers()
    {
        $servers = option(
            'queue\connect.redis.servers',
            $this->sourceConfig['servers']
        );

        if (array_key_exists('password', $servers) &&
            null === $servers['password']) {
            unset($servers['password']);
        }

        return $servers;
    }

    /**
     * 获取 redis 服务器参数.
     *
     * @return array
     */
    protected function getOptions()
    {
        return option('queue\connect.redis.options', []);
    }
}
