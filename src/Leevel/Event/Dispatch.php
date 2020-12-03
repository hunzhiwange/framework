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
     */
    protected IContainer $container;

    /**
     * 注册的监听器.
     */
    protected array $listeners = [];

    /**
     * 通配符的监听器.
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
     * {@inheritDoc}
     */
    public function handle(object|string $event, ...$params): void
    {
        if (is_object($event)) {
            $name = $event::class;
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
     * {@inheritDoc}
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
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    public function has(object|string $event): bool
    {
        return [] !== $this->get($event);
    }

    /**
     * {@inheritDoc}
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
     */
    protected function normalizeEvent(object|string $event): string
    {
        return is_object($event) ? $event::class : $event;
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
