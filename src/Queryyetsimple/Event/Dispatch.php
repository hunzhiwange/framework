<?php

declare(strict_types=1);

/*
 * This file is part of the ************************ package.
 * _____________                           _______________
 *  ______/     \__  _____  ____  ______  / /_  _________
 *   ____/ __   / / / / _ \/ __`\/ / __ \/ __ \/ __ \___
 *    __/ / /  / /_/ /  __/ /  \  / /_/ / / / / /_/ /__
 *      \_\ \_/\____/\___/_/   / / .___/_/ /_/ .___/
 *         \_\                /_/_/         /_/
 *
 * The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\Event;

use Leevel\Di\IContainer;

/**
 * 事件.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.04.17
 *
 * @version 1.0
 */
class Dispatch implements IDispatch
{
    /**
     * 项目容器.
     *
     * @var \Leevel\Di\IContainer
     */
    protected $container;

    /**
     * 注册的监听器.
     *
     * @var array
     */
    protected $listeners = [];

    /**
     * 通配符的监听器.
     *
     * @var array
     */
    protected $wildcards = [];

    /**
     * 创建一个事件解析器.
     *
     * @param \Leevel\Di\IContainer $container
     */
    public function __construct(IContainer $container)
    {
        $this->container = $container;
    }

    /**
     * 执行一个事件.
     *
     * @param object|string $event
     * @param array         $params
     */
    public function run($event, ...$params)
    {
        if (is_object($event)) {
            $name = get_class($event);
        } else {
            $name = $event;

            // This may return object or string
            $event = $this->container->make($event);
        }

        array_unshift($params, $event);

        $listeners = $this->get($name);

        if (!$listeners) {
            return;
        }

        ksort($listeners);

        foreach ($listeners as $items) {
            $items = $this->makeSubject($items);
            $items->notify(...$params);
        }

        unset($listeners);
    }

    /**
     * 注册监听器.
     *
     * @param array|object|string $event
     * @param mixed               $listener
     * @param int                 $priority
     */
    public function register($event, $listener, int $priority = 500)
    {
        $event = is_object($event) ? [$event] : (array) $event;
        $priority = (int) $priority;

        foreach ($event as $item) {
            $item = $this->normalizeEvent($item);

            if (false !== strpos($item, '*')) {
                $this->wildcards[$item][$priority][] = $listener;
            } else {
                $this->listeners[$item][$priority][] = $listener;
            }
        }
    }

    /**
     * 获取一个事件监听器.
     *
     * @param object|string $event
     *
     * @return array
     */
    public function get($event)
    {
        $listeners = [];

        $event = $this->normalizeEvent($event);

        if (isset($this->listeners[$event])) {
            $listeners = $this->listeners[$event];
        }

        foreach ($this->wildcards as $key => $item) {
            $key = $this->prepareRegexForWildcard($key);

            if (preg_match($key, $event, $res)) {
                foreach ($item as $priority => $value) {
                    if (!isset($listeners[$priority])) {
                        $listeners[$priority] = [];
                    }

                    $listeners[$priority] = array_merge($listeners[$priority], $value);
                }
            }
        }

        return $listeners;
    }

    /**
     * 判断事件监听器是否存在.
     *
     * @param object|string $event
     *
     * @return bool
     */
    public function has($event)
    {
        return [] !== $this->get($event);
    }

    /**
     * 删除一个事件所有监听器.
     *
     * @param object|string $event
     */
    public function delete($event)
    {
        $event = $this->normalizeEvent($event);

        if (isset($this->listeners[$event])) {
            unset($this->listeners[$event]);
        }

        if (isset($this->wildcards[$event])) {
            unset($this->wildcards[$event]);
        }
    }

    /**
     * 创建监听器观察者角色主体.
     *
     * @param string $listeners
     *
     * @return \Leevel\Event\Subject
     */
    protected function makeSubject(array $listeners)
    {
        $subject = new Subject($this->container);

        foreach ($listeners as $item) {
            $subject->attachs($item);
        }

        return $subject;
    }

    /**
     * 格式化事件名字.
     *
     * @param object|string $event
     */
    protected function normalizeEvent($event)
    {
        return is_object($event) ? get_class($event) : $event;
    }

    /**
     * 通配符正则.
     *
     * @param string $regex
     *
     * @return string
     */
    protected function prepareRegexForWildcard($regex)
    {
        $regex = preg_quote($regex, '/');
        $regex = '/^'.str_replace('\*', '(\S+)', $regex).'$/';

        return $regex;
    }
}
