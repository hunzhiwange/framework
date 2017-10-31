<?php
// [$QueryPHP] The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\auth;

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

use Exception;
use InvalidArgumentException;
use queryyetsimple\support\manager as support_manager;

/**
 * manager 入口
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.09.07
 * @version 1.0
 */
class manager extends support_manager
{
    
    /**
     * 返回默认驱动
     *
     * @return string
     */
    public function getDefaultDriver() {
        return $this->objContainer ['option'] [$this->getOptionName ( $this->objContainer ['option'] [$this->getOptionName ( 'default' )] . '_default' )];
    }
    
    /**
     * 设置默认驱动
     *
     * @param string $strName            
     * @return void
     */
    public function setDefaultDriver($strName) {
        $this->objContainer ['option'] [$this->getOptionName ( $this->objContainer ['option'] [$this->getOptionName ( 'default' )] . '_default' )] = $strName;
    }
    
    /**
     * 取得配置命名空间
     *
     * @return string
     */
    protected function getOptionNamespace() {
        return 'auth';
    }
    
    /**
     * 创建连接对象
     *
     * @param string $strConnect            
     * @param array $arrOption            
     * @return object
     */
    protected function createConnect($strConnect, array $arrOption = []) {
        return $this->{'makeConnect' . ucwords ( $strConnect )} ( $arrOption );
    }
    
    /**
     * 创建 session 连接
     *
     * @param array $arrOption            
     * @return \queryyetsimple\auth\session
     */
    protected function makeConnectSession($arrOption = []) {
        return new session ( $arrOption = array_merge ( $this->getOption ( 'session', $arrOption ) ), $this->objContainer [$arrOption ['model']], $this->objContainer ['session'], $this->objContainer ['cookie'], $this->objContainer ['encryption'], $this->objContainer ['validate'] );
    }
    
    /**
     * 创建 token 连接
     *
     * @param array $arrOption            
     * @return \queryyetsimple\auth\token
     */
    protected function makeConnectToken($arrOption = []) {
        return new token ( $arrOption = array_merge ( $this->getOption ( 'token', $arrOption ) ), $this->objContainer [$arrOption ['model']], $this->objContainer ['session'], $this->objContainer ['cookie'], $this->objContainer ['encryption'], $this->objContainer ['validate'], $this->objContainer ['cache']);
    }
}
