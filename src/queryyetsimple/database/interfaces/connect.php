<?php
// [$QueryPHP] A PHP Framework Since 2010.10.03. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\database\interfaces;

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
 * connect 接口
 *
 * @author Xiangmin Liu<635750556@qq.com>
 * @package $$
 * @since 2017.04.23
 * @version 1.0
 */
interface connect {
    
    /**
     * dsn 解析
     *
     * @param array $arrOption            
     * @return string
     */
    public function parseDsn($arrOption);
    
    /**
     * 取得数据库表名列表
     *
     * @param string $sDbName            
     * @param mixed $mixMaster            
     * @return array
     */
    public function getTableNames($sDbName = null, $mixMaster = false);
    
    /**
     * 取得数据库表字段信息
     *
     * @param string $sTableName            
     * @param mixed $mixMaster            
     * @return array
     */
    public function getTableColumns($sTableName, $mixMaster = false);
    
    /**
     * sql 字段格式化
     *
     * @return string
     */
    public function identifierColumn($sName);
}
