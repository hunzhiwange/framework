<?php
// [$QueryPHP] A PHP Framework Since 2010.10.03. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\queue\queues;

<<<queryphp
##########################################################
#   ____                          ______  _   _ ______   #
#  /     \       ___  _ __  _   _ | ___ \| | | || ___ \  #
# |   (  ||(_)| / _ \| '__|| | | || |_/ /| |_| || |_/ /  #
#  \____/ |___||  __/| |   | |_| ||  __/ |  _  ||  __/   #
#       \__   | \___ |_|    \__  || |    | | | || |      #
#     Query Yet Simple      __/  |\_|    |_| |_|\_|      #
#                          |___ /  Since 2010.10.03      #
##########################################################
queryphp;

use PHPQueue\Base;
use queryyetsimple\queue\queue;
use queryyetsimple\option\option;
use queryyetsimple\queue\backend\redis as backend_redis;

/**
 * redis 消息队列
 *
 * @author Xiangmin Liu<635750556@qq.com>
 * @package $$
 * @since 2017.05.11
 * @version 1.0
 */
class redis extends queue {
    
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
            'redis_options' => [ ] 
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
    public function __construct() {
        parent::__construct ();
        $this->arrSourceConfig ['servers'] = option::gets ( 'queue\connect.redis.servers', $this->arrSourceConfig ['servers'] );
        $this->arrSourceConfig ['redis_options'] = option::gets ( 'queue\connect.redis.options', [ ] );
        $this->resDataSource = new backend_redis ( $this->arrSourceConfig );
    }
    
    /**
     * 取得消息队列长度
     *
     * @return int
     */
    public function getQueueSize() {
    }
}