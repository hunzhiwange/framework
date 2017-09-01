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
use queryyetsimple\http\router;
use queryyetsimple\mvc\interfaces\view as interfaces_view;
use queryyetsimple\mvc\interfaces\controller as interfaces_controller;

/**
 * 基类控制器
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2016.11.19
 * @version 1.0
 */
abstract class controller implements interfaces_controller {
    
    /**
     * 视图
     *
     * @var \queryyetsimple\mvc\interfaces\view
     */
    protected $objView;
    
    /**
     * 视图
     *
     * @var \queryyetsimple\router\router
     */
    protected $objRouter;
    
    /**
     * 构造函数
     *
     * @return void
     */
    public function __construct() {
    }
    
    /**
     * 设置视图
     *
     * @param \queryyetsimple\mvc\interfaces\view $objView            
     * @return $this
     */
    public function setView(interfaces_view $objView) {
        $this->objView = $objView;
        return $this;
    }
    
    /**
     * 设置路由
     *
     * @param \queryyetsimple\router\router $objRouter            
     * @return $this
     */
    public function setRouter(router $objRouter) {
        $this->objRouter = $objRouter;
        return $this;
    }
    
    /**
     * 执行子方法器
     *
     * @param string $sActionName
     *            方法名
     * @return void
     */
    public function action($sActionName) {
        // 判断是否存在方法
        if (method_exists ( $this, $sActionName )) {
            $arrArgs = func_get_args ();
            array_shift ( $arrArgs );
            return call_user_func_array ( [ 
                    $this,
                    $sActionName 
            ], $arrArgs );
        }
        
        // 执行默认方法器
        if (! $this->objRouter)
            throw new RuntimeException ( 'Router is not set in controller' );
        return $this->objRouter->doBind ( null, $sActionName );
    }
    
    // ######################################################
    // ---------------- 实现 view 接口 start ----------------
    // ######################################################
    
    /**
     * 变量赋值
     *
     * @param mixed $mixName            
     * @param mixed $mixValue            
     * @return $this
     */
    public function assign($mixName, $mixValue = null) {
        $this->checkView ();
        $this->objView->assign ( $mixName, $mixValue );
        return $this;
    }
    
    /**
     * 获取变量赋值
     *
     * @param string|null $sName            
     * @return mixed
     */
    public function getAssign($sName = null) {
        $this->checkView ();
        return $this->objView->getVar ( $sName );
    }
    
    /**
     * 删除变量值
     *
     * @param mixed $mixName            
     * @return $this
     */
    public function deleteAssign($mixName) {
        $this->checkView ();
        call_user_func_array ( [ 
                $this->objView,
                'deleteAssign' 
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
        $this->checkView ();
        $this->objView->clearAssign ();
        return $this;
    }
    
    /**
     * 加载视图文件
     *
     * @param string $sThemeFile            
     * @param array $in
     *            charset 编码
     *            content_type 类型
     * @return string
     */
    public function display($sThemeFile = '', $arrOption = []) {
        $this->checkView ();
        return $this->objView->display ( $sThemeFile, $arrOption );
    }
    
    // ######################################################
    // ---------------- 实现 view 接口 end ----------------
    // ######################################################
    
    /**
     * 验证 view
     *
     * @return void
     */
    protected function checkView() {
        if (! $this->objView)
            throw new RuntimeException ( 'View is not set in controller' );
    }
    
    /**
     * 赋值
     *
     * @param mixed $mixName            
     * @param mixed $Value            
     * @return void
     */
    public function __set($mixName, $mixValue) {
        $this->assign ( $mixName, $mixValue );
    }
    
    /**
     * 获取值
     *
     * @param string $sName            
     * @return mixed
     */
    public function __get($sName) {
        return $this->getAssign ( $sName );
    }
}
