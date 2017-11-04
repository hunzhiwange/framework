<?php
// [$QueryPHP] The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\support;

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
use queryyetsimple\support\icontainer;

/**
 * manager 入口
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.09.07
 * @version 1.0
 */
abstract class manager implements imanager {
    
    /**
     * IOC Container
     *
     * @var \queryyetsimple\support\icontainer
     */
    protected $objContainer;
    
    /**
     * 连接对象
     *
     * @var object[]
     */
    protected $arrConnect;
    
    /**
     * 构造函数
     *
     * @param \queryyetsimple\support\icontainer $objContainer            
     * @return void
     */
    public function __construct(icontainer $objContainer) {
        $this->objContainer = $objContainer;
    }
    
    /**
     * 返回 IOC 容器
     *
     * @return \queryyetsimple\support\icontainer
     */
    public function container() {
        return $this->objContainer;
    }
    
    /**
     * 连接 connect 并返回连接对象
     *
     * @param array|string $mixOption            
     * @return object
     */
    public function connect($mixOption = []) {
        list ( $mixOption, $strUnique ) = $this->parseOptionAndUnique ( $mixOption );
        
        if (isset ( $this->arrConnect [$strUnique] )) {
            return $this->arrConnect [$strUnique];
        }
        
        $strDriver = ! empty ( $mixOption ['driver'] ) ? $mixOption ['driver'] : $this->getDefaultDriver ();
        return $this->arrConnect [$strUnique] = $this->makeConnect ( $strDriver, $mixOption );
    }
    
    /**
     * 重新连接
     *
     * @param array|string $mixOption            
     * @return object
     */
    public function reconnect($mixOption = []) {
        $this->disconnect ( $mixOption );
        return $this->connect ( $mixOption );
    }
    
    /**
     * 删除连接
     *
     * @param array|string $mixOption            
     * @return void
     */
    public function disconnect($mixOption = []) {
        list ( $mixOption, $strUnique ) = $this->parseOptionAndUnique ( $mixOption );
        
        if (isset ( $this->arrConnect [$strUnique] ))
            unset ( $this->arrConnect [$strUnique] );
    }
    
    /**
     * 取回所有连接
     *
     * @return object[]
     */
    public function getConnects() {
        return $this->arrConnect;
    }
    
    /**
     * 返回默认驱动
     *
     * @return string
     */
    public function getDefaultDriver() {
        return $this->objContainer ['option'] [$this->getOptionName ( 'default' )];
    }
    
    /**
     * 设置默认驱动
     *
     * @param string $strName            
     * @return void
     */
    public function setDefaultDriver($strName) {
        $this->objContainer ['option'] [$this->getOptionName ( 'default' )] = $strName;
    }
    
    /**
     * 取得配置命名空间
     *
     * @return string
     */
    abstract protected function getOptionNamespace();
    
    /**
     * 创建连接对象
     *
     * @param string $strConnect            
     * @param array $arrOption            
     * @return object
     */
    abstract protected function createConnect($strConnect, array $arrOption = []);
    
    /**
     * 取得连接名字
     *
     * @param string $strOptionName            
     * @return string
     */
    protected function getOptionName($strOptionName = null) {
        return $this->getOptionNamespace () . '\\' . $strOptionName;
    }
    
    /**
     * 创建连接
     *
     * @param string $strConnect            
     * @param array $arrOption            
     * @return object
     */
    protected function makeConnect($strConnect, array $arrOption = []) {
        if (is_null ( $this->objContainer ['option'] [$this->getOptionName ( 'connect.' . $strConnect )] ))
            throw new Exception ( sprintf ( 'Connect driver %s not exits', $strConnect ) );
        return $this->createConnect ( $strConnect, $arrOption );
    }
    
    /**
     * 分析连接参数以及其唯一值
     *
     * @param array|string $mixOption            
     * @return array
     */
    protected function parseOptionAndUnique($mixOption = []) {
        return [ 
                $mixOption = $this->parseOptionParameter ( $mixOption ),
                $this->getUnique ( $mixOption ) 
        ];
    }
    
    /**
     * 分析连接参数
     *
     * @param array|string $mixOption            
     * @return array
     */
    protected function parseOptionParameter($mixOption = []) {
        if (is_string ( $mixOption ) && ! is_array ( ($mixOption = $this->objContainer ['option'] [$this->getOptionName ( 'connect.' . $mixOption )]) )) {
            $mixOption = [ ];
        }
        
        return $mixOption;
    }
    
    /**
     * 取得唯一值
     *
     * @param array $arrOption            
     * @return string
     */
    protected function getUnique($arrOption) {
        return md5 ( serialize ( $arrOption ) );
    }
    
    /**
     * 读取默认配置
     *
     * @param string $strConnect            
     * @param array $arrExtendOption            
     * @return array
     */
    protected function getOption($strConnect, array $arrExtendOption = []) {
        $arrOption = $this->objContainer ['option'] [$this->getOptionName ()];
        unset ( $arrOption ['default'], $arrOption ['connect'] );
        return array_merge ( $this->objContainer ['option'] [$this->getOptionName ( 'connect.' . $strConnect )], $arrOption, $arrExtendOption );
    }
    
    /**
     * 拦截匿名注册控制器方法
     *
     * @param 方法名 $sMethod            
     * @param 参数 $arrArgs            
     * @return mixed
     */
    public function __call($sMethod, $arrArgs) {
        return call_user_func_array ( [ 
                $this->connect (),
                $sMethod 
        ], $arrArgs );
    }
}
