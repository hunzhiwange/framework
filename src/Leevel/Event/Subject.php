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

use Closure;
use InvalidArgumentException;
use Leevel\Di\IContainer;
use SplObjectStorage;
use SplObserver;
use SplSubject;

/**
 * 观察者目标角色 subject.
 *
 * @see http://php.net/manual/zh/class.splsubject.php
 */
class Subject implements SplSubject
{
    /**
     * IOC 容器.
     *
     * @var \Leevel\Di\IContainer
     */
    protected IContainer $container;

    /**
     * 通知附加参数.
     *
     * @var array
     */
    protected array $notifyArgs = [];

    /**
     * 观察者角色 observer.
     *
     * @var \SplObjectStorage
     */
    protected SplObjectStorage $observers;

    /**
     * 构造函数.
     */
    public function __construct(IContainer $container)
    {
        $this->observers = new SplObjectStorage();
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function attach(SplObserver $observer): void
    {
        $this->observers->attach($observer);
    }

    /**
     * {@inheritdoc}
     */
    public function detach(SplObserver $observer): void
    {
        $this->observers->detach($observer);
    }

    /**
     * {@inheritdoc}
     *
     * @param array ...$args
     */
    public function notify(...$args): void
    {
        $this->notifyArgs = $args;
        /** @var \Leevel\Event\Observer $observer */
        foreach ($this->observers as $observer) {
            $observer->update($this);
        }
    }

    /**
     * 添加一个观察者角色.
     *
     * @param \Closure|\SplObserver|string $observer
     *
     * @throws \InvalidArgumentException
     */
    public function register(Closure|SplObserver|string $observer): void
    {
        if ($observer instanceof Closure) {
            $observer = new Observer($observer);
        } else {
            if (is_string($observer) &&
                is_string($observer = $this->container->make($observer))) {
                $e = sprintf('Observer `%s` is invalid.', $observer);

                throw new InvalidArgumentException($e);
            }

            if (!($observer instanceof SplObserver)) {
                if (!is_callable([$observer, 'handle'])) {
                    $e = sprintf('Observer `%s` is invalid.', get_class($observer));

                    throw new InvalidArgumentException($e);
                }

                $observer = new Observer(Closure::fromCallable([$observer, 'handle']));
            }
        }

        $this->attach($observer);
    }

    /**
     * 获取 IOC 容器.
     */
    public function getContainer(): IContainer
    {
        return $this->container;
    }

    /**
     * 获取通知附加参数.
     */
    public function getNotifyArgs(): array
    {
        return $this->notifyArgs;
    }
}
