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
namespace Queryyetsimple\Event;

use Queryyetsimple\Di\IContainer;

/**
 * 事件
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.04.17
 * @version 1.0
 */
class Dispatch implements IDispatch
{

    /**
     * 项目容器
     *
     * @var \Queryyetsimple\Di\IContainer
     */
    protected $container;

    /**
     * 注册的监听器
     *
     * @var array
     */
    protected $listener = [];

    /**
     * 创建一个事件解析器
     *
     * @param \Queryyetsimple\Di\IContainer $container
     * @return void
     */
    public function __construct(IContainer $container)
    {
        $this->container = $container;
    }

    /**
     * 执行一个事件
     *
     * @param string|object $event
     * @return void
     */
    public function run($event)
    {
        if (is_object($event)) {
            $object = $event;
            $event = get_class($event);
        } else {
            $object = $this->container->make($event);
        }

        $args = func_get_args();
        array_shift($args);
        if (is_object($object)) {
            array_unshift($args, $object);
        }

        if (! $this->hasListener($event)) {
            return;
        }

        $this->getListener($event)->{'notify'}(...$args);
    }

    /**
     * 注册监听器
     *
     * @param string|array $event
     * @param mixed $listener
     * @return void
     */
    public function listener($event, $listener)
    {
        foreach (( array ) $event as $item) {
            $this->registerSubject($item);
            $this->listener[$item]->attachs($listener);
        }
    }

    /**
     * 获取一个监听器
     *
     * @param string $event
     * @return array
     */
    public function getListener($event)
    {
        if (isset($this->listener[$event])) {
            return $this->listener[$event];
        }
        return null;
    }

    /**
     * 判断监听器是否存在
     *
     * @param string $event
     * @return bool
     */
    public function hasListener($event)
    {
        return isset($this->listener[$event]);
    }

    /**
     * 删除一个事件所有监听器
     *
     * @param string $event
     * @return void
     */
    public function deleteListener($event)
    {
        if (isset($this->listener[$event])) {
            unset($this->listener[$event]);
        }
    }

    /**
     * 注册观察者角色主体
     *
     * @param string $event
     * @return void
     */
    protected function registerSubject($event)
    {
        if (! isset($this->listener[$event])) {
            $this->listener[$event] = new Subject($this->container);
        }
    }
}
