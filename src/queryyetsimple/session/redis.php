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

use queryyetsimple\cache\redis as cache_redis;
use queryyetsimple\classs\faces as classs_faces;
use queryyetsimple\session\abstracts\session as abstracts_session;
use queryyetsimple\session\interfaces\session as interfaces_session;

/**
 * session.redis
 *
 * @author Xiangmin Liu<635750556@qq.com>
 * @package $$
 * @since 2017.06.05
 * @version 1.0
 */
class redis extends abstracts_session implements interfaces_session {
    
    use classs_faces;
    
    /**
     * 配置
     *
     * @var array
     */
    protected $arrClasssFacesOption = [ 
            'session\connect.redis.host' => '127.0.0.1',
            'session\connect.redis.port' => 6379,
            'session\connect.redis.password' => '',
            'session\connect.redis.select' => 0,
            'session\connect.redis.timeout' => 0,
            'session\connect.redis.persistent' => false,
            'session\connect.redis.serialize' => true,
            'session\connect.redis.prefix' => null,
            'session\connect.redis.expire' => null 
    ];
    
    /**
     * (non-PHPdoc)
     *
     * @see SessionHandler::open()
     */
    public function open($strSavePath, $strName) {
        $this->objCache = (new cache_redis ( $this->arrOption ))->initClasssFacesOptionDefault ();
        return true;
    }
}
