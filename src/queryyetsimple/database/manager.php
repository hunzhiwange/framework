<?php
// [$QueryPHP] The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\database;

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

use queryyetsimple\support\helper;
use queryyetsimple\support\manager as support_manager;

/**
 * database 入口
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.02.15
 * @version 1.0
 */
class manager extends support_manager {

    /**
     * 取得配置命名空间
     *
     * @return string
     */
    protected function getOptionNamespace() {
        return 'database';
    }

    /**
     * 创建连接对象
     *
     * @param string $strConnect            
     * @param array $arrOption            
     * @return object
     */
    protected function createConnect($strConnect, array $arrOption = []) {
        return new database ( $this->{'makeConnect' . ucwords ( $strConnect )} ( $arrOption ) );
    }
    
    /**
     * 创建 mysql 连接
     *
     * @param array $arrOption            
     * @return \queryyetsimple\database\mysql
     */
    protected function makeConnectMysql($arrOption = []) {
        return new mysql ( $this->objContainer ['log']->connect (), $this->getOption ( 'mysql', is_array ( $arrOption ) ? $arrOption : [ ] ) );
    }
    
    /**
     * 读取默认配置
     *
     * @param string $strConnect            
     * @param array $arrExtendOption            
     * @return array
     */
    protected function getOption($strConnect, array $arrExtendOption = []) {
        return $this->parseOption ( parent::getOption($strConnect,  $arrExtendOption) );
    }
    
    /**
     * 分析数据库配置参数
     *
     * @param array $arrOption            
     * @return array
     */
    protected function parseOption($arrOption) {
        $arrTemp = $arrOption;
        
        foreach ( array_keys ( $arrOption ) as $strType ) {
            if (in_array ( $strType, [ 
                    'distributed',
                    'readwrite_separate',
                    'driver',
                    'master',
                    'slave',
                    'fetch',
                    'log' 
            ] )) {
                if (isset ( $arrTemp [$strType] ))
                    unset ( $arrTemp [$strType] );
            } else {
                if (isset ( $arrOption [$strType] ))
                    unset ( $arrOption [$strType] );
            }
        }
        
        // 纠正数据库服务器参数
        foreach ( [ 
                'master',
                'slave' 
        ] as $strType ) {
            if (! is_array ( $arrOption [$strType] ))
                $arrOption [$strType] = [ ];
        }
        
        // 填充数据库服务器参数
        $arrOption ['master'] = array_merge ( $arrOption ['master'], $arrTemp );
        
        // 是否采用分布式服务器，非分布式关闭附属服务器
        if (! $arrOption ['distributed']) {
            $arrOption ['slave'] = [ ];
        } elseif ($arrOption ['slave']) {
            if (count ( $arrOption ['slave'] ) == count ( $arrOption ['slave'], COUNT_RECURSIVE )) {
                $arrOption ['slave'] = [ 
                        $arrOption ['slave'] 
                ];
            }
            foreach ( $arrOption ['slave'] as &$arrSlave ) {
                $arrSlave = array_merge ( $arrSlave, $arrTemp );
            }
        }
        
        // + 合并支持
        $arrOption = helper::arrayMergePlus ( $arrOption );
        
        // 返回结果
        unset ( $arrTemp );
        return $arrOption;
    }

}
