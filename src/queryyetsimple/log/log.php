<?php
// [$QueryPHP] The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\log;

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

use RuntimeException;
use queryyetsimple\support\ijson;
use queryyetsimple\support\assert;
use queryyetsimple\support\option;
use queryyetsimple\support\iarray;

/**
 * 日志仓储
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.03.03
 * @version 1.0
 */
class log implements ilog {
    
    use option;
    
    /**
     * 存储连接对象
     *
     * @var \queryyetsimple\log\iconnect
     */
    protected $oConnect;
    
    /**
     * 当前记录的日志信息
     *
     * @var array
     */
    protected $arrLog = [ ];
    
    /**
     * 日志过滤器
     *
     * @var callable
     */
    protected $calFilter;
    
    /**
     * 日志处理器
     *
     * @var callable
     */
    protected $calProcessor;
    
    /**
     * 配置
     *
     * @var array
     */
    protected $arrOption = [ 
            'enabled' => true,
            'level' => [ 
                    self::DEBUG,
                    self::INFO,
                    self::NOTICE,
                    self::WARNING,
                    self::ERROR,
                    self::CRITICAL,
                    self::ALERT,
                    self::EMERGENCY,
                    self::SQL 
            ],
            'time_format' => '[Y-m-d H:i]' 
    ];
    
    /**
     * 构造函数
     *
     * @param \queryyetsimple\log\iconnect $oConnect            
     * @param array $arrOption            
     * @return void
     */
    public function __construct(iconnect $oConnect, array $arrOption = []) {
        $this->oConnect = $oConnect;
        $this->options ( $arrOption );
    }
    
    // ######################################################
    // ------ 实现 \Psr\Log\LoggerInterface 接口 start -------
    // ######################################################
    
    /**
     * 记录 emergency 日志
     *
     * @param string $mixMessage            
     * @param array $arrContext            
     * @param boolean $booWrite            
     * @return array
     */
    public function emergency($mixMessage, array $arrContext = [], $booWrite = false) {
        return $this->{$booWrite ? 'write' : 'log'} ( static::EMERGENCY, $mixMessage, $arrContext );
    }
    
    /**
     * 记录 alert 日志
     *
     * @param string $mixMessage            
     * @param array $arrContext            
     * @param boolean $booWrite            
     * @return array
     */
    public function alert($mixMessage, array $arrContext = [], $booWrite = false) {
        return $this->{$booWrite ? 'write' : 'log'} ( static::ALERT, $mixMessage, $arrContext );
    }
    
    /**
     * 记录 critical 日志
     *
     * @param string $mixMessage            
     * @param array $arrContext            
     * @param boolean $booWrite            
     * @return array
     */
    public function critical($mixMessage, array $arrContext = [], $booWrite = false) {
        return $this->{$booWrite ? 'write' : 'log'} ( static::CRITICAL, $mixMessage, $arrContext );
    }
    
    /**
     * 记录 error 日志
     *
     * @param string $mixMessage            
     * @param array $arrContext            
     * @param boolean $booWrite            
     * @return array
     */
    public function error($mixMessage, array $arrContext = [], $booWrite = false) {
        return $this->{$booWrite ? 'write' : 'log'} ( static::ERROR, $mixMessage, $arrContext );
    }
    
    /**
     * 记录 warning 日志
     *
     * @param string $mixMessage            
     * @param array $arrContext            
     * @param boolean $booWrite            
     * @return array
     */
    public function warning($mixMessage, array $arrContext = [], $booWrite = false) {
        return $this->{$booWrite ? 'write' : 'log'} ( static::WARNING, $mixMessage, $arrContext );
    }
    
    /**
     * 记录 notice 日志
     *
     * @param string $mixMessage            
     * @param array $arrContext            
     * @param boolean $booWrite            
     * @return array
     */
    public function notice($mixMessage, array $arrContext = [], $booWrite = false) {
        return $this->{$booWrite ? 'write' : 'log'} ( static::NOTICE, $mixMessage, $arrContext );
    }
    
    /**
     * 记录 info 日志
     *
     * @param string $mixMessage            
     * @param array $arrContext            
     * @param boolean $booWrite            
     * @return array
     */
    public function info($mixMessage, array $arrContext = [], $booWrite = false) {
        return $this->{$booWrite ? 'write' : 'log'} ( static::INFO, $mixMessage, $arrContext );
    }
    
    /**
     * 记录 debug 日志
     *
     * @param string $mixMessage            
     * @param array $arrContext            
     * @param boolean $booWrite            
     * @return array
     */
    public function debug($mixMessage, array $arrContext = [], $booWrite = false) {
        return $this->{$booWrite ? 'write' : 'log'} ( static::DEBUG, $mixMessage, $arrContext );
    }
    
    /**
     * 记录日志
     *
     * @param string $strLevel            
     * @param mixed $mixMessage            
     * @param array $arrContext            
     * @return array
     */
    public function log($strLevel, $mixMessage, array $arrContext = []) {
        // 是否开启日志
        if (! $this->getOption ( 'enabled' )) {
            return;
        }
        
        // 只记录系统允许的日志级别
        if (! in_array ( $strLevel, $this->getOption ( 'level' ) )) {
            return;
        }
        
        $mixMessage = $this->formatMessage ( $mixMessage );
        
        $arrData = [ 
                $strLevel,
                $mixMessage,
                $arrContext 
        ];
        
        // 执行过滤器
        if ($this->calFilter !== null && call_user_func_array ( $this->calFilter, $arrData ) === false) {
            return;
        }
        
        // 日志消息
        $mixMessage = date ( $this->getOption ( 'time_format' ) ) . $mixMessage;
        
        // 记录到内存方便后期调用
        if (! isset ( $this->arrLog [$strLevel] )) {
            $this->arrLog [$strLevel] = [ ];
        }
        $this->arrLog [$strLevel] [] = $arrData;
        
        return $arrData;
    }
    
    // ######################################################
    // ------- 实现 \Psr\Log\LoggerInterface 接口 end --------
    // ######################################################
    
    /**
     * 记录错误消息并写入
     *
     * @param string $strLevel
     *            日志类型
     * @param string $strMessage
     *            应该被记录的错误信息
     * @param array $arrContext            
     * @return void
     */
    public function write($strLevel, $strMessage, array $arrContext = []) {
        $this->saveStore ( [ 
                $this->log ( $strLevel, $strMessage, $arrContext ) 
        ] );
    }
    
    /**
     * 保存日志信息
     *
     * @return void
     */
    public function save() {
        if (! $this->arrLog)
            return;
        
        foreach ( $this->arrLog as $arrData ) {
            $this->saveStore ( $arrData );
        }
        
        $this->clear ();
    }
    
    /**
     * 注册日志过滤器
     *
     * @param callable $calFilter            
     * @return void
     */
    public function registerFilter(callable $calFilter) {
        assert::callback ( $calFilter );
        $this->calFilter = $calFilter;
    }
    
    /**
     * 注册日志处理器
     *
     * @param callable $calProcessor            
     * @return void
     */
    public function registerProcessor(callable $calProcessor) {
        assert::callback ( $calProcessor );
        $this->calProcessor = $calProcessor;
    }
    
    /**
     * 清理日志记录
     *
     * @param string $strLevel            
     * @return int
     */
    public function clear($strLevel = null) {
        if ($strLevel && isset ( $this->arrLog [$strLevel] )) {
            $nCount = count ( $this->arrLog [$strLevel] );
            $this->arrLog [$strLevel] = [ ];
        } else {
            $nCount = count ( $this->arrLog );
            $this->arrLog = [ ];
        }
        
        return $nCount;
    }
    
    /**
     * 获取日志记录
     *
     * @param string $strLevel            
     * @return array
     */
    public function get($strLevel = null) {
        if ($strLevel && isset ( $this->arrLog [$strLevel] )) {
            return $this->arrLog [$strLevel];
        } else {
            return $this->arrLog;
        }
    }
    
    /**
     * 获取日志记录数量
     *
     * @param string $strLevel            
     * @return int
     */
    public function count($strLevel = null) {
        if ($strLevel && isset ( $this->arrLog [$strLevel] )) {
            return count ( $this->arrLog [$strLevel] );
        } else {
            return count ( $this->arrLog );
        }
    }
    
    /**
     * 存储日志
     *
     * @param array $arrData            
     * @return void
     */
    protected function saveStore($arrData) {
        // 执行处理器
        if ($this->calProcessor !== null) {
            call_user_func_array ( $this->calProcessor, $arrData );
        }
        $this->oConnect->save ( $arrData );
    }
    
    /**
     * 格式化日志消息
     *
     * @param mixed $mixMessage            
     * @return mixed
     */
    protected function formatMessage($mixMessage) {
        if (is_array ( $mixMessage )) {
            return var_export ( $mixMessage, true );
        } elseif ($mixMessage instanceof ijson) {
            return $mixMessage->toJson ();
        } elseif ($mixMessage instanceof iarray) {
            return var_export ( $mixMessage->toArray (), true );
        } elseif (is_scalar ( $mixMessage )) {
            return $mixMessage;
        }
        
        throw new RuntimeException ( 'Message is invalid' );
    }
    
    /**
     * 缺省方法
     *
     * @param 方法名 $sMethod            
     * @param 参数 $arrArgs            
     * @return mixed
     */
    public function __call($sMethod, $arrArgs) {
        return call_user_func_array ( [ 
                $this->oConnect,
                $sMethod 
        ], $arrArgs );
    }
}
