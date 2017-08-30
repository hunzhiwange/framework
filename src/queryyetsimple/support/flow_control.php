<?php
// [$QueryPHP] The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\support;

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

/**
 * 流程控制复用
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.04.13
 * @version 1.0
 */
trait flow_control {
    
    /**
     * 逻辑代码是否处于条件表达式中
     *
     * @var boolean
     */
    protected $booInFlowControl = false;
    
    /**
     * 条件表达式是否为真
     *
     * @var boolean
     */
    protected $booFlowControlIsTrue = false;
    
    /**
     * 条件语句 ifs
     *
     * @param boolean $booValue            
     * @return $this
     */
    public function ifs($booValue = false) {
        return $this->setFlowControl ( true, $booValue );
    }
    
    /**
     * 条件语句 elseIfs
     *
     * @param boolean $booValue            
     * @return $this
     */
    public function elseIfs($booValue = false) {
        return $this->setFlowControl ( true, $booValue );
    }
    
    /**
     * 条件语句 elses
     *
     * @return $this
     */
    public function elses() {
        return $this->setFlowControl ( true, ! $this->getFlowControl ()[1] );
    }
    
    /**
     * 条件语句 endIfs
     *
     * @return $this
     */
    public function endIfs() {
        return $this->setFlowControl ( false, false );
    }
    
    /**
     * 设置当前条件表达式状态
     *
     * @param boolean $booInFlowControl            
     * @param boolean $booFlowControlIsTrue            
     * @return void
     */
    protected function setFlowControl($booInFlowControl, $booFlowControlIsTrue) {
        $this->booInFlowControl = $booInFlowControl;
        $this->booFlowControlIsTrue = $booFlowControlIsTrue;
        return $this;
    }
    
    /**
     * 获取当前条件表达式状态
     *
     * @return array
     */
    protected function getFlowControl() {
        return [ 
                $this->booInFlowControl,
                $this->booFlowControlIsTrue 
        ];
    }
    
    /**
     * 验证一下条件表达式是否通过
     *
     * @return boolean
     */
    protected function checkFlowControl() {
        return $this->booInFlowControl && ! $this->booFlowControlIsTrue;
    }
    
    /**
     * 占位符
     *
     * @param string $strMethod            
     * @return boolean
     */
    protected function placeholderFlowControl($strMethod) {
        return in_array ( $strMethod, [ 
                'placeholder',
                'foobar' 
        ] );
    }
}
