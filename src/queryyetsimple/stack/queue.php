<?php
// [$QueryPHP] A PHP Framework Since 2010.10.03. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\stack;

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

use SplQueue;
use InvalidArgumentException;
use queryyetsimple\stack\traits\base;

/**
 * 队列，先进先出
 *
 * @author Xiangmin Liu<635750556@qq.com>
 * @package $$
 * @since 2016.11.21
 * @see http://php.net/manual/zh/class.splqueue.php
 * @version 1.0
 */
class queue extends SplQueue {
    
    use base;
    
    /**
     * 入对
     *
     * @param mixed $mixValue            
     * @return void
     */
    public function in($mixValue) {
        $this->enqueue ( $mixValue );
    }
    
    /**
     * 出对
     *
     * @return mixed
     */
    public function out() {
        return $this->dequeue ();
    }
    
    /**
     * 验证类型是否正确遇到错误抛出异常
     *
     * @param mixed $mixValue            
     * @return void
     */
    public function checkTypeWithException($mixValue) {
        if (! $this->checkType ( $mixValue ))
            throw new InvalidArgumentException ( __ ( '队列元素类型验证失败，允许类型为 %s', implode ( ',', $this->arrType ) ) );
    }
}
