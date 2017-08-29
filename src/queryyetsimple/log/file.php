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
use queryyetsimple\classs\option;
use queryyetsimple\filesystem\fso;
use queryyetsimple\log\interfaces\connect;

/**
 * log.file
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.06.05
 * @version 1.0
 */
class file implements connect {
    
    use option;
    
    /**
     * 配置
     *
     * @var array
     */
    protected $arrOption = [ 
            'name' => 'Y-m-d H',
            'size' => 2097152,
            'path' => '' 
    ];
    
    /**
     * 构造函数
     *
     * @param array $arrOption            
     * @return void
     */
    public function __construct(array $arrOption = []) {
        $this->options ( $arrOption );
    }
    
    /**
     * 日志写入接口
     *
     * @param array $arrData
     *            message 消息
     *            level 级别
     *            message_type
     *            destination
     *            extraHeaders
     * @return void
     */
    public function save(array $arrData) {
        // 保存日志
        $this->checkSize ( $strDestination = $this->getPath ( $arrData ['level'] ) );
        
        // 记录到系统
        error_log ( $arrData ['message'] . PHP_EOL, 3, $strDestination );
    }
    
    /**
     * 验证日志文件大小
     *
     * @param string $sFilePath            
     * @return void
     */
    protected function checkSize($sFilePath) {
        // 如果不是文件，则创建
        if (! is_file ( $sFilePath ) && ! is_dir ( dirname ( $sFilePath ) ) && ! fso::createDirectory ( dirname ( $sFilePath ) )) {
            throw new RuntimeException ( __ ( '无法创建日志文件：%s', $sFilePath ) );
        }
        
        // 检测日志文件大小，超过配置大小则备份日志文件重新生成
        if (is_file ( $sFilePath ) && floor ( $this->getOption ( 'size' ) ) <= filesize ( $sFilePath )) {
            rename ( $sFilePath, dirname ( $sFilePath ) . '/' . date ( 'Y-m-d H.i.s' ) . '~@' . basename ( $sFilePath ) );
        }
    }
    
    /**
     * 获取日志路径
     *
     * @param string $strLevel            
     * @param string $sFilePath            
     * @return string
     */
    protected function getPath($strLevel) {
        // 不存在路径，则直接使用项目默认路径
        if (empty ( $sFilePath )) {
            if (! $this->getOption ( 'path' )) {
                throw new RuntimeException ( __ ( '未指定日志默认路径' ) );
            }
            $sFilePath = $this->getOption ( 'path' ) . '/' . $strLevel . '/' . date ( $this->getOption ( 'name' ) ) . ".log";
        }
        return $sFilePath;
    }
}
