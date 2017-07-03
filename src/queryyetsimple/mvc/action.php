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
use queryyetsimple\mvc\interfaces\action as interfaces_action;

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
    protected $objController = null;
    
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
    public function setController($objController) {
        $this->objController = $objController;
        return $this;
    }
    
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
        $this->checkController ();
        return call_user_func_array ( [ 
                $this->objController,
                $sMethod 
        ], $arrArgs );
    }
}
