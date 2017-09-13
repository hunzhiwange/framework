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
use BadFunctionCallException;
use queryyetsimple\router\router;
use queryyetsimple\mvc\interfaces\view as interfaces_view;
use queryyetsimple\mvc\interfaces\action as interfaces_action;
use queryyetsimple\mvc\interfaces\controller as interfaces_controller;

/**
 * 基类方法器
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2016.11.19
 * @version 1.0
 */
abstract class action implements interfaces_action {
    
    /**
     * 父控制器
     *
     * @var \queryyetsimple\mvc\interfaces\controller
     */
    protected $objController;
    
    /**
     * 构造函数
     *
     * @return void
     */
    public function __construct() {
    }
    
    /**
     * 设置父控制器
     *
     * @param \queryyetsimple\mvc\interfaces\controller $objController            
     * @return $this
     */
    public function setController(interfaces_controller $objController) {
        $this->objController = $objController;
        return $this;
    }
    
    // ######################################################
    // ------------- 实现 controller 接口 start -------------
    // ######################################################
    
    /**
     * 设置视图
     *
     * @param \queryyetsimple\mvc\interfaces\view $objView            
     * @return $this
     */
    public function setView(interfaces_view $objView) {
        $this->checkController ();
        return $this->objController->setView ( $objView );
    }
    
    /**
     * 设置路由
     *
     * @param \queryyetsimple\router\router $objRouter            
     * @return $this
     */
    public function setRouter(router $objRouter) {
        $this->checkController ();
        return $this->objController->setRouter ( $objRouter );
    }
    
    /**
     * 执行子方法器
     *
     * @param string $sActionName
     *            方法名
     * @return void
     */
    public function action($sActionName) {
        $this->checkController ();
        return $this->objController->action ( $sActionName );
    }
    
    // ######################################################
    // -------------- 实现 controller 接口 end --------------
    // ######################################################
    
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
        $this->checkController ();
        return $this->objController->assign ( $mixName, $mixValue );
    }
    
    /**
     * 获取变量赋值
     *
     * @param string|null $sName            
     * @return mixed
     */
    public function getAssign($sName = null) {
        $this->checkController ();
        return $this->objController->getAssign ( $sName );
    }
    
    /**
     * 删除变量值
     *
     * @param mixed $mixName            
     * @return $this
     */
    public function deleteAssign($mixName) {
        $this->checkController ();
        call_user_func_array ( [ 
                $this->objController,
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
        $this->checkController ();
        $this->objController->clearAssign ();
        return $this;
    }
    
    /**
     * 加载视图文件
     *
     * @param string $sThemeFile            
     * @param array $arrOption
     *            charset 编码
     *            content_type 类型
     * @return string
     */
    public function display($sThemeFile = '', $arrOption = []) {
        $this->checkController ();
        return $this->objController->display ( $sThemeFile, $arrOption );
    }
    
    // ######################################################
    // ---------------- 实现 view 接口 end ----------------
    // ######################################################
    
    /**
     * 验证 controller
     *
     * @return void
     */
    protected function checkController() {
        if (! $this->objController)
            throw new RuntimeException ( 'Controller is not set in action' );
    }
    
    /**
     * 访问父控制器
     *
     * @param string $sMethod            
     * @param array $arrArgs            
     * @return boolean
     */
    public function __call($sMethod, $arrArgs) {
        if ($sMethod == 'run') {
            throw new BadFunctionCallException ( __ ( '方法对象不允许通过 __call 方法执行  run 入口' ) );
        }
        throw new BadFunctionCallException ( 'Method %s is not defined' );
    }
}
