<?php
// [$QueryPHP] The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
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

use SessionHandlerInterface;
use queryyetsimple\session\abstracts\connect;
use queryyetsimple\cache\memcache as cache_memcache;

/**
 * session.memcache
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.06.05
 * @version 1.0
 */
class memcache extends connect implements SessionHandlerInterface {
    
    /**
     * 配置
     *
     * @var array
     */
    protected $arrOption = [ 
            'servers' => [ ],
            'host' => '127.0.0.1',
            'port' => 11211,
            'compressed' => false,
            'persistent' => false,
            'prefix' => null,
            'expire' => null 
    ];
    
    /**
     * (non-PHPdoc)
     *
     * @see \SessionHandler::open()
     */
    public function open($strSavePath, $strName) {
        $this->objCache = new cache_memcache ( $this->arrOption );
        return true;
    }
}
