<?php
// [$QueryPHP] A PHP Framework Since 2010.10.03. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\session;

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

use SessionHandler;
use SessionHandlerInterface;
use queryyetsimple\session\abstracts\connect;
use queryyetsimple\cache\redis as cache_redis;

/**
 * session.redis
 *
 * @author Xiangmin Liu<635750556@qq.com>
 * @package $$
 * @since 2017.06.05
 * @version 1.0
 */
class redis extends connect implements SessionHandlerInterface {
    
    /**
     * 配置
     *
     * @var array
     */
    protected $arrOption = [ 
            'host' => '127.0.0.1',
            'port' => 6379,
            'password' => '',
            'select' => 0,
            'timeout' => 0,
            'persistent' => false,
            'serialize' => true,
            'prefix' => null,
            'expire' => null 
    ];
    
    /**
     * (non-PHPdoc)
     *
     * @see SessionHandler::open()
     */
    public function open($strSavePath, $strName) {
        $this->objCache = new cache_redis ( $this->arrOption );
        return true;
    }
}
