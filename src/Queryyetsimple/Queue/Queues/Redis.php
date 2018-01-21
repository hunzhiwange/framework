<?php
/*
 * This file is part of the ************************ package.
 * ##########################################################
 * #   ____                          ______  _   _ ______   #
 * #  /     \       ___  _ __  _   _ | ___ \| | | || ___ \  #
 * # |   (  ||(_)| / _ \| '__|| | | || |_/ /| |_| || |_/ /  #
 * #  \____/ |___||  __/| |   | |_| ||  __/ |  _  ||  __/   #
 * #       \__   | \___ |_|    \__  || |    | | | || |      #
 * #     Query Yet Simple      __/  |\_|    |_| |_|\_|      #
 * #                          |___ /  Since 2010.10.03      #
 * ##########################################################
 *
 * The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Queryyetsimple\Queue\Queues;

use PHPQueue\Base;
use Queryyetsimple\Queue\Backend\Redis as BackendRedis;

/**
 * redis 消息队列
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.05.11
 * @version 1.0
 */
class Redis extends Queue implements IQueue
{

    /**
     * 队列连接
     *
     * @var string
     */
    protected $strConnect = 'redis';

    /**
     * 连接配置
     *
     * @var array
     */
    protected $arrSourceConfig = [
        'servers' => [
            'host' => '127.0.0.1',
            'port' => 6379
        ],
        'redis_options' => []
    ];

    /**
     * 队列执行者
     *
     * @var string
     */
    protected $strQueueWorker = 'redis';

    /**
     * 构造函数
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->arrSourceConfig['servers'] = $this->getServers();
        $this->arrSourceConfig['redis_options'] = $this->getOptions();
        $this->resDataSource = new BackendRedis($this->arrSourceConfig);
    }

    /**
     * 取得消息队列长度
     *
     * @return int
     */
    public function getQueueSize()
    {
    }

    /**
     * 获取 redis 服务器主机
     *
     * @return array
     */
    protected function getServers()
    {
        $arrServers = option('queue\connect.redis.servers', $this->arrSourceConfig['servers']);
        if (array_key_exists('password', $arrServers) && is_null($arrServers['password'])) {
            unset($arrServers['password']);
        }
        return $arrServers;
    }

    /**
     * 获取 redis 服务器参数
     *
     * @return array
     */
    protected function getOptions()
    {
        return option('queue\connect.redis.options', [ ]);
    }
}
