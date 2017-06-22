<?php
// [$QueryPHP] The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\mvc;

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

use queryyetsimple\view\interfaces\theme;

/**
 * 视图
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2016.11.19
 * @version 1.0
 */
class view {
    
    /**
     * 视图模板
     *
     * @var \queryyessimple\view\interfaces\theme
     */
    protected $objTheme;
    
    /**
     * 构造函数
     *
     * @return void
     */
    public function __construct() {
    }
    
    /**
     * 注册视图模板
     *
     * @param \queryyetsimple\view\interfaces\theme $objTheme            
     * @return $this
     */
    public function registerTheme(theme $objTheme) {
        $this->objTheme = $objTheme;
        return $this;
    }
    
    /**
     * 变量赋值
     *
     * @param mixed $mixName            
     * @param mixed $mixValue            
     * @return mixed
     */
    public function assign($mixName, $mixValue = null) {
        return $this->objTheme->setVar ( $mixName, $mixValue );
    }
    
    /**
     * 获取变量赋值
     *
     * @param string|null $sName            
     * @return mixed
     */
    public function getAssign($sName = null) {
        return $this->objTheme->getVar ( $sName );
    }
    
    /**
     * 加载视图文件
     *
     * @param string $sFile            
     * @param array $in
     *            charset 编码
     *            content_type 内容类型
     *            return 是否返回
     * @return void|string
     */
    public function display($sFile = '', $in = []) {
        $in = array_merge ( [ 
                'charset' => 'utf-8',
                'content_type' => 'text/html',
                'return' => false 
        ], $in );
        
        // 设置 header
        if (! headers_sent ()) {
            header ( "Content-Type:" . $in ['content_type'] . "; charset=" . $in ['charset'] );
            
            // 支持页面回跳
            header ( "Cache-control: private" );
        }
        
        $sContent = $this->objTheme->display ( $sFile, false );
        if ($in ['return'] === true) {
            return $sContent;
        } else {
            echo $sContent;
            unset ( $sContent );
        }
    }
}
