<?php
// [$QueryPHP] A PHP Framework Since 2010.10.03. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\stack\traits;

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
 * 栈和队列基础 trait
 *
 * @author Xiangmin Liu<635750556@qq.com>
 * @package $$
 * @since 2017.05.23
 * @version 1.0
 */
trait base {
    
    /**
     * 允许的类型
     *
     * @var array
     */
    private $arrType = [ ];
    
    /**
     * 构造函数
     *
     * @return void
     */
    public function __construct(/* args */){
        $this->arrType = func_get_args ();
    }
    
    /**
     * 如果一个空的结构出元素会抛出 RuntimeException 异常
     *
     * @see \SplDoublyLinkedList::pop()
     * @return void
     */
    public function pop() {
        if ($this->isEmpty ())
            return null;
        return parent::pop ();
    }
    
    /**
     * 如果一个空的 queue 弹出元素会抛出 RuntimeException 异常
     *
     * @see \SplQueue::dequeue()
     * @return mixed
     */
    public function dequeue() {
        if ($this->isEmpty ())
            return null;
        return parent::dequeue ();
    }
    
    /**
     * Adds an element to the queue.
     *
     * @param mixed $mixValue            
     * @see \SplQueue::enqueue()
     * @return void
     */
    public function enqueue($mixValue) {
        $this->checkTypeWithException ( $mixValue );
        parent::enqueue ( $mixValue );
    }
    
    /**
     * Add/insert a new value at the specified index
     *
     * @param mixed $mixValue            
     * @param mixed $mixNewval            
     * @see \SplDoublyLinkedList::pop()
     * @return void
     */
    public function add($mixIndex, $mixNewval) {
        $this->checkTypeWithException ( $mixNewval );
        parent::add ( $mixIndex, $mixNewval );
    }
    
    /**
     * Sets the value at the specified $mixIndex to $mixNewval
     *
     * @param mixed $mixValue            
     * @param mixed $mixNewval            
     * @see \SplDoublyLinkedList::offsetSet()
     * @return void
     */
    public function offsetSet($mixIndex, $mixNewval) {
        $this->checkTypeWithException ( $mixNewval );
        parent::offsetSet ( $mixIndex, $mixNewval );
    }
    
    /**
     * Pushes an element at the end of the doubly linked list
     *
     * @param mixed $mixValue            
     * @see \SplDoublyLinkedList::push()
     * @return void
     */
    public function push($mixValue) {
        $this->checkTypeWithException ( $mixValue );
        parent::push ( $mixValue );
    }
    
    /**
     * Prepends the doubly linked list with an element
     *
     * @param mixed $mixValue            
     * @see \SplDoublyLinkedList::unshift()
     * @return void
     */
    public function unshift($mixValue) {
        $this->checkTypeWithException ( $mixValue );
        parent::unshift ( $mixValue );
    }
    
    /**
     * 验证类型是否正确
     *
     * @param mixed $mixValue            
     * @return bool
     */
    public function checkType($mixValue) {
        if (! count ( $this->arrType ))
            return true;
        return call_user_func_array ( [ 
                'queryyetsimple\helper\helper',
                'isThese' 
        ], [ 
                $mixValue,
                $this->arrType 
        ] );
    }
}
