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
use queryyetsimple\assert\assert;
use queryyetsimple\classs\option;
use queryyetsimple\log\interfaces\connect;
use queryyetsimple\log\interfaces\store as interfaces_store;

/**
 * 日志存储
 *
 * @author Xiangmin Liu<635750556@qq.com>
 * @package $$
 * @since 2017.03.03
 * @version 1.0
 */
class store implements interfaces_store {
    
    use option;
    
    /**
     * 存储连接对象
     *
     * @var \queryyetsimple\log\interfaces\connect
     */
    protected $oConnect;
    
    /**
     * debug
     *
     * @var string
     */
    const DEBUG = 'debug';
    
    /**
     * info
     *
     * @var string
     */
    const INFO = 'info';
    
    /**
     * notice
     *
     * @var string
     */
    const NOTICE = 'notice';
    
    /**
     * warning
     *
     * @var string
     */
    const WARNING = 'warning';
    
    /**
     * error
     *
     * @var string
     */
    const ERROR = 'error';
    
    /**
     * critical
     *
     * @var string
     */
    const CRITICAL = 'critical';
    
    /**
     * alert
     *
     * @var string
     */
    const ALERT = 'alert';
    
    /**
     * emergency
     *
     * @var string
     */
    const EMERGENCY = 'emergency';
    
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
    protected $calFilter = null;
    
    /**
     * 日志处理器
     *
     * @var callable
     */
    protected $calProcessor = null;
    
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
                    self::EMERGENCY 
            ],
            'time_format' => '[Y-m-d H:i]' 
    ];
    
    /**
     * 构造函数
     *
     * @param \queryyetsimple\log\interfaces\connect $oConnect            
     * @param array $arrOption            
     * @return void
     */
    public function __construct(connect $oConnect, array $arrOption = []) {
        $this->oConnect = $oConnect;
        $this->options ( $arrOption );
    }
    
    /**
     * 记录错误消息
     *
     * @param string $strMessage
     *            应该被记录的错误信息
     * @param string $strLevel
     *            日志类型
     * @return array
     */
    public function record($strMessage, $strLevel = 'info') {
        // 是否开启日志
        if (! $this->getOption ( 'enabled' )) {
            return;
        }
        
        // 只记录系统允许的日志级别
        if (! in_array ( $strLevel, $this->getOption ( 'level' ) )) {
            return;
        }
        
        // 执行过滤器
        if ($this->calFilter !== null && call_user_func_array ( $this->calFilter, [ 
                $strMessage,
                $strLevel 
        ] ) === false) {
            return;
        }
        
        // 日志消息
        $strMessage = date ( $this->getOption ( 'time_format' ) ) . $strMessage;
        
        // 记录到内存方便后期调用
        if (! isset ( $this->arrLog [$strLevel] )) {
            $this->arrLog [$strLevel] = [ ];
        }
        $this->arrLog [$strLevel] [] = $strMessage;
        
        return [ 
                'message' => $strMessage,
                'level' => $strLevel 
        ];
    }
    
    /**
     * 记录错误消息并写入
     *
     * @param string $strMessage
     *            应该被记录的错误信息
     * @param string $strLevel
     *            日志类型
     * @return void
     */
    public function write($strMessage, $strLevel = 'info') {
        $this->saveStore ( $this->record ( $strMessage, $strLevel ) );
    }
    
    /**
     * 保存日志信息
     *
     * @return void
     */
    public function save() {
        if (! $this->arrLog)
            return;
        
        foreach ( $this->arrLog as $strLevel => $arrData ) {
            $this->saveStore ( [ 
                    'level' => $strLevel,
                    'message' => implode ( PHP_EOL, $arrData ) 
            ] );
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
     * @return number
     */
    public function clear() {
        $nCount = count ( $this->arrLog );
        $this->arrLog = [ ];
        return $nCount;
    }
    
    /**
     * 获取日志记录
     *
     * @return array
     */
    public function get() {
        return $this->arrLog;
    }
    
    /**
     * 获取日志记录数量
     *
     * @return number
     */
    public function count() {
        return count ( $this->arrLog );
    }
    
    /**
     * 存储日志
     *
     * @param array $arrData
     *            message 消息
     *            level 级别
     * @return void
     */
    protected function saveStore($arrData) {
        // 执行处理器
        if ($this->calProcessor !== null) {
            call_user_func_array ( $this->calProcessor, [ 
                    $arrData ['message'],
                    $arrData ['level'] 
            ] );
        }
        
        $this->oConnect->save ( $arrData );
    }
}
