<?php
// [$QueryPHP] The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\observer;

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

use SplSubject;
use SplObserver;
use SplObjectStorage;
use InvalidArgumentException;
use queryyetsimple\support\interfaces\container;
use queryyetsimple\observer\interfaces\subject as interfaces_subject;

/**
 * 观察者目标角色 subject
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.06.23
 * @version 1.0
 */
abstract class subject implements SplSubject, interfaces_subject {
    
    /**
     * 容器
     *
     * @var \queryyetsimple\support\interfaces\container
     */
    protected $objContainer;
    
    /**
     * 观察者角色 observer
     *
     * @var \SplObjectStorage(\SplObserver)
     */
    protected $objObservers;
    
    /**
     * 构造函数
     *
     * @param \queryyetsimple\support\interfaces\container $objContainer            
     * @return void
     */
    public function __construct(container $objContainer) {
        $this->objObservers = new SplObjectStorage ();
        $this->objContainer = $objContainer;
    }
    
    /**
     * (non-PHPdoc)
     *
     * @see SplSubject::attach()
     */
    public function attach(SplObserver $objObserver) {
        $this->objObservers->attach ( $objObserver );
    }
    
    /**
     * (non-PHPdoc)
     *
     * @see SplSubject::detach()
     */
    public function detach(SplObserver $objObserver) {
        $this->objObservers->detach ( $objObserver );
    }
    
    /**
     * (non-PHPdoc)
     *
     * @see SplSubject::notify()
     */
    public function notify() {
        $arrArgs = func_get_args ();
        array_unshift ( $arrArgs, $this );
        
        foreach ( $this->objObservers as $objObserver ) {
            call_user_func_array ( [ 
                    $objObserver,
                    'update' 
            ], $arrArgs );
        }
        unset ( $arrArgs );
    }
    
    /**
     * 添加一个观察者角色
     *
     * @param \SplObserver|string $mixObserver            
     * @return $this
     */
    public function attachs($mixObserver) {
        if (is_string ( $mixObserver )) {
            $strObserver = $mixObserver;
            
            if (($mixObserver = $this->objContainer->make ( $mixObserver )) === false)
                throw new InvalidArgumentException ( sprintf ( 'Observer %s is not valid.', $strObserver ) );
        }
        
        if ($mixObserver instanceof SplObserver) {
            $this->objObservers->attach ( $mixObserver );
        } else {
            throw new InvalidArgumentException ( 'Invalid observer argument because it not instanceof SplObserver' );
        }
    }
    
    /**
     * 返回容器
     *
     * @return \queryyetsimple\support\interfaces\container
     */
    public function container() {
        return $this->objContainer;
    }
}