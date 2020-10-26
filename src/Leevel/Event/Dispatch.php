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
 * (c) 2010-2020 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\Event;

use Leevel\Di\IContainer;

/**
 * 事件.
 */
class Dispatch implements IDispatch
{
    /**
     * IOC 容器.
     *
     * @var \Leevel\Di\IContainer
     */
    protected IContainer $container;

    /**
     * 注册的监听器.
     *
     * @var array
     */
    protected array $listeners = [];

    /**
     * 通配符的监听器.
     *
     * @var array
     */
    protected array $wildcards = [];

    /**
     * 创建一个事件解析器.
     */
    public function __construct(IContainer $container)
    {
        $this->container = $container;
    }

    /**
     * 执行一个事件.
     *
     * @param object|string $event
     * @param array         ...$params
     */
    public function handle(object|string $event, ...$params): void
    {
        if (is_object($event)) {
            $name = get_class($event);
        } else {
            $name = $event;
            $event = $this->container->make($event);
        }

        array_unshift($params, $event);

        $listeners = $this->get($name);
        if (!$listeners) {
            return;
        }

        ksort($listeners);
        $listeners = array_reduce($listeners, function (array $result, array $value) {
            return array_merge($result, $value);
        }, []);

        $this->makeSubject($listeners)->notify(...$params);
    }

    /**
     * 注册监听器.
     *
     * @param array|object|string $event
     * @param mixed               $listener
     */
    public function register(array|object|string $event, mixed $listener, int $priority = 500): void
    {
        $event = is_object($event) ? [$event] : (array) $event;
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
     */
    public function get(object|string $event): array
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
                    $listeners[$priority] = array_merge($listeners[$priority] ?? [], $value);
                }
            }
        }

        return $listeners;
    }

    /**
     * 判断事件监听器是否存在.
     *
     * @param object|string $event
     */
    public function has(object|string $event): bool
    {
        return [] !== $this->get($event);
    }

    /**
     * 删除事件所有监听器.
     *
     * @param object|string $event
     */
    public function delete(object|string $event): void
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
     */
    protected function makeSubject(array $listeners): Subject
    {
        $subject = new Subject($this->container);
        foreach ($listeners as $item) {
            $subject->register($item);
        }

        return $subject;
    }

    /**
     * 格式化事件名字.
     *
     * @param object|string $event
     */
    protected function normalizeEvent(object|string $event): string
    {
        return is_object($event) ? get_class($event) : $event;
    }

    /**
     * 通配符正则.
     */
    protected function prepareRegexForWildcard(string $regex): string
    {
        $regex = preg_quote($regex, '/');
        $regex = '/^'.str_replace('\*', '(\S*)', $regex).'$/';

        return $regex;
    }
}
