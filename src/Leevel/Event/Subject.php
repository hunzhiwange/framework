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
 * (c) 2010-2019 http://queryphp.com All rights reserved.
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
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.06.23
 * @see http://php.net/manual/zh/class.splsubject.php
 *
 * @version 1.0
 */
class Subject implements ISubject, SplSubject
{
    /**
     * 容器.
     *
     * @var \Leevel\Di\IContainer
     */
    public IContainer $container;

    /**
     * 通知附加参数.
     *
     * @var array
     */
    public array $notifyArgs = [];

    /**
     * 观察者角色 observer.
     *
     * @var \SplObjectStorage
     */
    protected SplObjectStorage $observers;

    /**
     * 构造函数.
     *
     * @param \Leevel\Di\IContainer $container
     */
    public function __construct(IContainer $container)
    {
        $this->observers = new SplObjectStorage();
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function attach(SplObserver $observer)
    {
        $this->observers->attach($observer);
    }

    /**
     * {@inheritdoc}
     */
    public function detach(SplObserver $observer)
    {
        $this->observers->detach($observer);
    }

    /**
     * {@inheritdoc}
     */
    public function notify(...$args)
    {
        $this->notifyArgs = $args;

        foreach ($this->observers as $observer) {
            $observer->update($this);
        }
    }

    /**
     * 添加一个观察者角色.
     *
     * @param \Closure|\SplObserver|string $observer
     */
    public function register($observer): void
    {
        if ($observer instanceof Closure) {
            $observer = new Observer($observer);
        } else {
            if (is_string($observer) &&
                is_string($observer = $this->container->make($observer))) {
                throw new InvalidArgumentException(
                    sprintf('Observer `%s` is invalid.', $observer)
                );
            }

            if (!($observer instanceof SplObserver)) {
                if (!is_callable([$observer, 'handle'])) {
                    throw new InvalidArgumentException(
                        sprintf('Observer `%s` is invalid.', get_class($observer))
                    );
                }

                $observer = new Observer(Closure::fromCallable([$observer, 'handle']));
            }
        }

        $this->attach($observer);
    }
}
