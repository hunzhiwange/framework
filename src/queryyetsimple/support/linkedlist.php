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

use SplDoublyLinkedList;
use InvalidArgumentException;

/**
 * 双向链表
 * 在 PHP 双向链表的基础上加上数据类型验证功能，不少业务场景中保证链表中数据一致性
 * 以及链表返回空数据时阻止抛出异常的默认行为
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2016.11.21
 * @see http://php.net/manual/zh/class.spldoublylinkedlist.php
 * @version 1.0
 */
class linkedlist extends SplDoublyLinkedList {
    
    /**
     * 允许的类型
     *
     * @var array
     */
    protected $arrType = [ ];
    
    /**
     * 构造函数
     *
     * @param mixed $mixArgs            
     * @return void
     */
    public function __construct($mixArgs = null) {
        $this->arrType = is_array ( $mixArgs ) ? $mixArgs : func_get_args ();
    }
    
    /**
     * (non-PHPdoc)
     *
     * @see SplDoublyLinkedList::pop()
     */
    public function pop() {
        if ($this->isEmpty ())
            return null;
        return parent::pop ();
    }
    
    /**
     * (non-PHPdoc)
     *
     * @see SplDoublyLinkedList::add()
     */
    public function add($mixIndex, $mixNewval) {
        $this->validate ( $mixNewval );
        parent::add ( $mixIndex, $mixNewval );
    }
    
    /**
     * (non-PHPdoc)
     *
     * @see SplDoublyLinkedList::offsetSet()
     */
    public function offsetSet($mixIndex, $mixNewval) {
        $this->validate ( $mixNewval );
        parent::offsetSet ( $mixIndex, $mixNewval );
    }
    
    /**
     * (non-PHPdoc)
     *
     * @see SplDoublyLinkedList::push()
     */
    public function push($mixValue) {
        $this->validate ( $mixValue );
        parent::push ( $mixValue );
    }
    
    /**
     * (non-PHPdoc)
     *
     * @see SplDoublyLinkedList::unshift()
     */
    public function unshift($mixValue) {
        $this->validate ( $mixValue );
        parent::unshift ( $mixValue );
    }
    
    /**
     * 验证类型是否正确遇到错误抛出异常
     *
     * @param mixed $mixValue            
     * @return void
     */
    public function validate($mixValue) {
        if (! $this->checkType ( $mixValue ))
            throw new InvalidArgumentException ( sprintf ( 'The linkedlist element type verification failed, and the allowed type is %s.', implode ( ',', $this->arrType ) ) );
    }
    
    /**
     * 验证类型是否正确
     *
     * @param mixed $mixValue            
     * @return bool
     */
    protected function checkType($mixValue) {
        if (! count ( $this->arrType ))
            return true;
        return call_user_func_array ( [ 
                'queryyetsimple\support\helper',
                'varThese' 
        ], [ 
                $mixValue,
                $this->arrType 
        ] );
    }
}
