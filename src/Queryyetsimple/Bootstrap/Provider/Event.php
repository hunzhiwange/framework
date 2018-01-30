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
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Queryyetsimple\Bootstrap\Provider;

use Queryyetsimple\{
    Di\Provider,
    Event\IDispatch
};

/**
 * 事件服务提供者
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.04.26
 * @version 1.0
 */
class Event extends Provider
{
    
    /**
     * 监听器列表
     *
     * @var array
     */
    protected $listeners = [];
    
    /**
     * 注册时间监听器
     *
     * @param \Queryyetsimple\Event\IDispatch $dispatch
     * @return void
     */
    public function bootstrap(IDispatch $dispatch)
    {
        foreach ($this->getListeners() as $event => $listeners) {
            foreach ($listeners as $key => $item) {
                if (is_int($item)) {
                    $dispatch->listeners($event, $key, $item);
                } else {
                    $dispatch->listeners($event, $item);
                }
            }
        }
    }
    
    /**
     * 注册一个提供者
     *
     * @return void
     */
    public function register()
    {
    }
    
    /**
     * 取得监听器
     *
     * @return array
     */
    public function getListeners()
    {
        return $this->listeners;
    }
}
