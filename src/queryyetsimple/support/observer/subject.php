<?php
/*
 * This file is part of the ************************ package.
 * ##########################################################
 * #   ____                          ______  _   _ ______   #
 * #  /     \       ___  _ __  _   _ | ___ \| | | || ___ \  #
 * # |   (  ||(_)| / _ \| '__|| | | || |_/ /| |_| || |_/ /  #
 * #  \____/ |___||  __/| |   | |_| ||  __/ |  _  ||  __/   #
 * #       \__   | \___ |_|    \__  || |    | | | || |      #
 * #     Query Yet Simple      __/  |\_|    |_| |_|\_|      #
 * #                          |___ /  Since 2010.10.03      #
 * ##########################################################
 * 
 * The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
 * (c) 2010-2017 http://queryphp.com All rights reserved.
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace queryyetsimple\support\observer;

use SplSubject;
use SplObserver;
use SplObjectStorage;
use InvalidArgumentException;
use queryyetsimple\support\icontainer;

/**
 * 观察者目标角色 subject
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.06.23
 * @version 1.0
 */
abstract class subject implements SplSubject, isubject
{
    
    /**
     * 容器
     *
     * @var \queryyetsimple\support\icontainer
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
     * @param \queryyetsimple\support\icontainer $objContainer
     * @return void
     */
    public function __construct(icontainer $objContainer)
    {
        $this->objObservers = new SplObjectStorage();
        $this->objContainer = $objContainer;
    }
    
    /**
     * (non-PHPdoc)
     *
     * @see SplSubject::attach()
     */
    public function attach(SplObserver $objObserver)
    {
        $this->objObservers->attach($objObserver);
    }
    
    /**
     * (non-PHPdoc)
     *
     * @see SplSubject::detach()
     */
    public function detach(SplObserver $objObserver)
    {
        $this->objObservers->detach($objObserver);
    }
    
    /**
     * (non-PHPdoc)
     *
     * @see SplSubject::notify()
     */
    public function notify()
    {
        $arrArgs = func_get_args();
        array_unshift($arrArgs, $this);
        
        foreach ($this->objObservers as $objObserver) {
            call_user_func_array([
                $objObserver, 
                'update'
            ], $arrArgs);
        }
        unset($arrArgs);
    }
    
    /**
     * 添加一个观察者角色
     *
     * @param \SplObserver|string $mixObserver
     * @return $this
     */
    public function attachs($mixObserver)
    {
        if (is_string($mixObserver)) {
            $strObserver = $mixObserver;
            
            if (($mixObserver = $this->objContainer->make($mixObserver)) === false) {
                throw new InvalidArgumentException(sprintf('Observer %s is not valid.', $strObserver));
            }
        }
        
        if ($mixObserver instanceof SplObserver) {
            $this->objObservers->attach($mixObserver);
        } else {
            throw new InvalidArgumentException('Invalid observer argument because it not instanceof SplObserver');
        }
    }

    /**
     * 返回容器
     *
     * @return \queryyetsimple\support\icontainer
     */
    public function container()
    {
        return $this->objContainer;
    }
}
