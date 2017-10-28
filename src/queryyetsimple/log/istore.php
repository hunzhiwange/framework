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

use Psr\Log\LoggerInterface;

/**
 * istore 接口
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.04.11
 * @version 1.0
 */
interface istore extends LoggerInterface {
    
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
     * sql
     *
     * @var string
     */
    const SQL = 'sql';
    
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
    public function write($strLevel, $strMessage, array $arrContext = []);
    
    /**
     * 保存日志信息
     *
     * @return void
     */
    public function save();
    
    /**
     * 注册日志过滤器
     *
     * @param callable $calFilter            
     * @return void
     */
    public function registerFilter(callable $calFilter);
    
    /**
     * 注册日志处理器
     *
     * @param callable $calProcessor            
     * @return void
     */
    public function registerProcessor(callable $calProcessor);
    
    /**
     * 清理日志记录
     *
     * @param string $strLevel            
     * @return int
     */
    public function clear($strLevel = null);
    
    /**
     * 获取日志记录
     *
     * @param string $strLevel            
     * @return array
     */
    public function get($strLevel = null);
    
    /**
     * 获取日志记录数量
     *
     * @param string $strLevel            
     * @return int
     */
    public function count($strLevel = null);
}
