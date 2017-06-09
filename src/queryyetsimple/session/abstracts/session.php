<?php
// [$QueryPHP] A PHP Framework Since 2010.10.03. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\session\abstracts;

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

/**
 * session 驱动抽象类
 *
 * @author Xiangmin Liu<635750556@qq.com>
 * @package $$
 * @since 2017.06.06
 * @version 1.0
 */
abstract class session extends SessionHandler {
    
    /**
     * redis
     *
     * @var \queryyetsimple\cache\redis
     */
    protected $objCache = null;
    
    /**
     * 配置
     *
     * @var array
     */
    protected $arrOption = [ ];
    
    /**
     * 构造函数
     *
     * @param array $arrOption            
     * @return void
     */
    public function __construct($arrOption = []) {
        $this->initialization ( $arrOption );
    }
    
    /**
     * (non-PHPdoc)
     *
     * @see SessionHandler::close()
     */
    public function close() {
        $this->gc ( ini_get ( 'session.gc_maxlifetime' ) );
        $this->objCache->close ();
        return true;
    }
    
    /**
     * (non-PHPdoc)
     *
     * @see SessionHandler::read()
     */
    public function read($strSessID) {
        return $this->objCache->get ( $this->getSessionName ( $strSessID ) );
    }
    
    /**
     * (non-PHPdoc)
     *
     * @see SessionHandler::write()
     */
    public function write($strSessID, $mixSessData) {
        $this->objCache->set ( $this->getSessionName ( $strSessID ), $mixSessData );
    }
    
    /**
     * (non-PHPdoc)
     *
     * @see SessionHandler::destroy()
     */
    public function destroy($strSessID) {
        $this->objCache->delele ( $this->getSessionName ( $strSessID ) );
    }
    
    /**
     * (non-PHPdoc)
     *
     * @see SessionHandler::gc()
     */
    public function gc($intMaxlifetime) {
        return true;
    }
    
    /**
     * 初始化缓存配置
     *
     * @param array $arrOption            
     * @return void
     */
    protected function initialization($arrOption) {
        foreach ( array_keys ( $this->arrClasssFacesOption ) as $strOption ) {
            $arrTemp = explode ( '.', $strOption );
            $arrTemp = array_pop ( $arrTemp );
            $this->arrOption [$arrTemp] = $this->classsFacesOption ( $strOption );
        }
        
        if (is_array ( $arrOption )) {
            $this->arrOption = array_merge ( $this->arrOption, $arrOption );
        }
    }
    
    /**
     * 获取 session 名字
     *
     * @param string $strSessID            
     * @return string
     */
    protected function getSessionName($strSessID) {
        return $this->arrOption ['prefix'] . $strSessID;
    }
}
