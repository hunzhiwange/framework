<?php
// [$QueryPHP] The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\mail;

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

use queryyetsimple\support\manager as support_manager;

/**
 * mail 入口
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.08.26
 * @version 1.0
 */
class manager extends support_manager {
    
    /**
     * 取得配置命名空间
     *
     * @return string
     */
    protected function getOptionNamespace() {
        return 'mail';
    }
    
    /**
     * 创建连接对象
     *
     * @param object $objConnect            
     * @return object
     */
    protected function createConnect($objConnect) {
        return new mail ( $objConnect, $this->objContainer ['view'], $this->objContainer ['event'], $this->getOptionCommon () );
    }
    
    /**
     * 创建 smtp 连接
     *
     * @param array $arrOption            
     * @return \queryyetsimple\mail\smtp
     */
    protected function makeConnectSmtp($arrOption = []) {
        return new smtp ( array_merge ( $this->getOption ( 'smtp', $arrOption ) ) );
    }
    
    /**
     * 创建 sendmail 连接
     *
     * @param array $arrOption            
     * @return \queryyetsimple\mail\sendmail
     */
    protected function makeConnectSendmail($arrOption = []) {
        return new sendmail ( array_merge ( $this->getOption ( 'sendmail', $arrOption ) ) );
    }
    
    /**
     * 过滤全局配置项
     *
     * @return array
     */
    protected function filterOptionCommonItem() {
        return [ 
                'default',
                'connect',
                'from',
                'to' 
        ];
    }
}
