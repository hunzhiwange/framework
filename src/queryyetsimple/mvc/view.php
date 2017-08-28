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

use RuntimeException;
use queryyetsimple\view\interfaces\theme;
use queryyetsimple\mvc\interfaces\view as interfaces_view;

/**
 * 视图
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2016.11.19
 * @version 1.0
 */
class view implements interfaces_view {
    
    /**
     * 视图模板
     *
     * @var \queryyessimple\view\interfaces\theme
     */
    protected $objTheme;
    
    /**
     * 构造函数
     *
     * @param \queryyetsimple\view\interfaces\theme $objTheme            
     * @return void
     */
    public function __construct(theme $objTheme) {
        $this->objTheme = $objTheme;
    }
    
    /**
     * 变量赋值
     *
     * @param mixed $mixName            
     * @param mixed $mixValue            
     * @return $this
     */
    public function assign($mixName, $mixValue = null) {
        $this->checkTheme ();
        $this->objTheme->setVar ( $mixName, $mixValue );
        return $this;
    }
    
    /**
     * 获取变量赋值
     *
     * @param string|null $sName            
     * @return mixed
     */
    public function getAssign($sName = null) {
        $this->checkTheme ();
        return $this->objTheme->getVar ( $sName );
    }
    
    /**
     * 删除变量值
     *
     * @param mixed $mixName            
     * @return $this
     */
    public function deleteAssign($mixName) {
        $this->checkTheme ();
        call_user_func_array ( [ 
                $this->objTheme,
                'deleteVar' 
        ], func_get_args () );
        return $this;
    }
    
    /**
     * 清空变量值
     *
     * @param string|null $sName            
     * @return $this
     */
    public function clearAssign() {
        $this->checkTheme ();
        $this->objTheme->clearVar ();
        return $this;
    }
    
    /**
     * 加载视图文件
     *
     * @param string $sFile            
     * @param array $arrOption
     *            charset 编码
     *            content_type 内容类型
     *            return 是否返回
     * @return void|string
     */
    public function display($sFile = '', $arrOption = []) {
        $this->checkTheme ();
        $arrOption = array_merge ( [ 
                'charset' => 'utf-8',
                'content_type' => 'text/html',
                'return' => false 
        ], $arrOption );
        
        // 设置 header
        if (! headers_sent ()) {
            header ( "Content-Type:" . $arrOption ['content_type'] . "; charset=" . $arrOption ['charset'] );
            header ( "Cache-control: protected" );
        }
        
        $sContent = $this->objTheme->display ( $sFile, false );
        if ($arrOption ['return'] === true) {
            return $sContent;
        } else {
            echo $sContent;
            unset ( $sContent );
        }
    }
    
    /**
     * 验证 theme
     *
     * @return void
     */
    protected function checkTheme() {
        if (! $this->objTheme)
            throw new RuntimeException ( 'Theme is not set in view' );
    }
}
