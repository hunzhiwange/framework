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
use queryyetsimple\support\option;
use queryyetsimple\filesystem\fso;

/**
 * log.file
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.06.05
 * @version 1.0
 */
class file extends aconnect implements iconnect {
    
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
     * @return void
     */
    public function save(array $arrData) {
        // 保存日志
        $this->checkSize ( $strDestination = $this->getPath ( $arrData [0] [0] ) );
        
        // 记录到系统
        foreach ( $arrData as $arrItem ) {
            error_log ( $this->formatMessage ( $arrItem [1], $arrItem [2] ) . PHP_EOL, 3, $strDestination );
        }
    }
    
    /**
     * 格式化日志信息
     *
     * @param string $strMessage
     *            应该被记录的错误信息
     * @param array $arrContext            
     * @return string
     */
    protected function formatMessage($strMessage, array $arrContext = []) {
        return $strMessage . ' ' . json_encode ( $arrContext, JSON_UNESCAPED_UNICODE );
    }
}
