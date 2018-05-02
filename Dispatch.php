<?php
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

use RuntimeException;
use Leevel\Di\IContainer;

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
     * @var \Leevel\Di\IContainer
     */
    protected $container;

    /**
     * 注册的监听器
     *
     * @var array
     */
    protected $listeners = [];

    /**
     * 通配符的监听器
     *
     * @var array
     */
    protected $wildcards = [];

    /**
     * 通配符是否严格匹配
     *
     * @var boolean
     */
    protected $strict = false;
    
    /**
     * 创建一个事件解析器
     *
     * @param \Leevel\Di\IContainer $container
     * @param bool $event
     * @return void
     */
    public function __construct(IContainer $container, bool $strict = false)
    {
        $this->container = $container;
        $this->strict = $strict;
    }

    /**
     * 执行一个事件
     *
     * @param string|object $event
     * @param array $params
     * @return void
     */
    public function run($event, ...$params)
    {
        if (is_object($event)) {
            $object = $event;
            $event = get_class($event);
        } else {
            $object = $this->container->make($event);
            if (! is_object($object)) {
                throw new RuntimeException(sprintf('Event %s is invalid.', $event));
            }
        }

        array_unshift($params, $object);

        if (! $this->hasListeners($event)) {
            return;
        }

        $listeners = $this->getListeners($event);
        ksort($listeners);

        foreach($listeners as $items) {
            $items = $this->makeSubject($items);
            $items->{'notify'}(...$params);
        }

        unset($listeners);
    }

    /**
     * 注册监听器
     *
     * @param string|array $event
     * @param mixed $listener
     * @param int $priority
     * @return void
     */
    public function listeners($event, $listener, int $priority = 500)
    {
        $event = (array) $event;
        $priority = intval($priority);

        foreach ($event as $item) {
            if (strpos($item, '*') !== false) { 
                $this->wildcards[$item][$priority][] = $listener;
            } else {
                $this->listeners[$item][$priority][] = $listener;     
            }
        }
    }

    /**
     * 获取一个事件监听器
     *
     * @param string $event
     * @return array
     */
    public function getListeners($event)
    {
        $listeners = [];

        if (isset($this->listeners[$event])) {
            $listeners = $this->listeners[$event];
        }

        foreach ($this->wildcards as $key => $item) {
            $key = $this->prepareRegexForWildcard($key, $this->strict);

            if (preg_match($key, $event, $res)) {
                foreach ($item as $priority => $value) {
                    if (! isset($listeners[$priority])) {
                        $listeners[$priority]= [];
                    }

                    $listeners[$priority] = array_merge($listeners[$priority], $value);
                }
            }
        }

        return $listeners;
    }

    /**
     * 判断事件监听器是否存在
     *
     * @param string $event
     * @return bool
     */
    public function hasListeners($event)
    {
        return isset($this->listeners[$event]) || isset($this->wildcards[$event]);
    }

    /**
     * 删除一个事件所有监听器
     *
     * @param string $event
     * @return void
     */
    public function deleteListeners($event)
    {
        if (isset($this->listeners[$event])) {
            unset($this->listeners[$event]);
        }

        if (isset($this->wildcards[$event])) {
            unset($this->wildcards[$event]);
        }
    }

    /**
     * 设置是否严格匹配事件
     *
     * @param bool $event
     * @return $this
     */
    public function strict(bool $strict)
    {
        $this->strict = $strict;
        return $this;
    }

    /**
     * 创建监听器观察者角色主体
     *
     * @param string $listeners
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
     * 通配符正则
     *
     * @param string $strFoo
     * @param bool $strict
     * @return string
     */
    protected function prepareRegexForWildcard($regex, $strict = true)
    {
        return '/^' . str_replace('6084fef57e91a6ecb13fff498f9275a7', '(\S+)', $this->escapeRegexCharacter(str_replace('*', '6084fef57e91a6ecb13fff498f9275a7', $regex))) . ($strict ? '$' : '') . '/';
    }

    /**
     * 转义正则表达式特殊字符
     *
     * @param string $txt
     * @return string
     */
    protected function escapeRegexCharacter($txt)
    {
        $txt = str_replace([
            '$',
            '/',
            '?',
            '*',
            '.',
            '!',
            '-',
            '+',
            '(',
            ')',
            '[',
            ']',
            ',',
            '{',
            '}',
            '|',
            '\\'
        ], [
            '\$',
            '\/',
            '\\?',
            '\\*',
            '\\.',
            '\\!',
            '\\-',
            '\\+',
            '\\(',
            '\\)',
            '\\[',
            '\\]',
            '\\,',
            '\\{',
            '\\}',
            '\\|',
            '\\\\'
        ], $txt);

        return $txt;
    }
}
