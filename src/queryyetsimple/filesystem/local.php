<?php
// [$QueryPHP] The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\filesystem;

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

use InvalidArgumentException;
use queryyetsimple\filesystem\abstracts\connect;
use League\Flysystem\Adapter\Local as AdapterLocal;
use queryyetsimple\filesystem\interfaces\connect as interfaces_connect;

/**
 * filesystem.local
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.08.29
 * @see https://flysystem.thephpleague.com/adapter/local/
 * @version 1.0
 */
class local extends connect implements interfaces_connect {
    
    /**
     * 配置
     *
     * @var array
     */
    protected $arrOption = [ 
            'path' => '',
            'write_flags' => LOCK_EX,
            'link_handling' => AdapterLocal::DISALLOW_LINKS,
            'permissions' => [ ] 
    ];
    
    /**
     * 创建连接
     *
     * @return \League\Flysystem\AdapterInterface
     */
    public function makeConnect() {
        if (empty ( $this->getOption ( 'path' ) )) {
            throw new InvalidArgumentException ( 'The local requires path option' );
        }
        
        return new AdapterLocal ( $this->getOption ( 'path' ), $this->getOption ( 'write_flags' ), $this->getOption ( 'link_handling' ), $this->getOption ( 'permissions' ) );
    }
}
