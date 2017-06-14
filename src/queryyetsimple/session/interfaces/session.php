<?php
// [$QueryPHP] A PHP Framework Since 2010.10.03. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\session\interfaces;

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

/**
 * session 接口
 *
 * @author Xiangmin Liu<635750556@qq.com>
 * @package $$
 * @since 2017.04.11
 * @version 1.0
 */
interface session {
    
    /**
     * (non-PHPdoc)
     *
     * @see SessionHandler::open()
     */
    public function open($strSavePath, $strName);
    
    /**
     * (non-PHPdoc)
     *
     * @see SessionHandler::close()
     */
    public function close();
    
    /**
     * (non-PHPdoc)
     *
     * @see SessionHandler::read()
     */
    public function read($strSessID);
    
    /**
     * (non-PHPdoc)
     *
     * @see SessionHandler::write()
     */
    public function write($strSessID, $mixSessData);
    
    /**
     * (non-PHPdoc)
     *
     * @see SessionHandler::destroy()
     */
    public function destroy($strSessID);
    
    /**
     * (non-PHPdoc)
     *
     * @see SessionHandler::gc()
     */
    public function gc($intMaxlifetime);
}
