<?php

declare(strict_types=1);

namespace Leevel\Event;

use Leevel\Di\IContainer;
use SplSubject;

/**
 * 观察者目标角色 subject.
 *
 * @see http://php.net/manual/zh/class.splsubject.php
 */
class Subject implements \SplSubject
{
    /**
     * IOC 容器.
     */
    protected IContainer $container;

    /**
     * 通知附加参数.
     */
    protected array $notifyArgs = [];

    /**
     * 观察者角色 observer.
     */
    protected \SplObjectStorage $observers;

    /**
     * 构造函数.
     */
    public function __construct(IContainer $container)
    {
        $this->observers = new \SplObjectStorage();
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function attach(\SplObserver $observer): void
    {
        $this->observers->attach($observer);
    }

    /**
     * {@inheritDoc}
     */
    public function detach(\SplObserver $observer): void
    {
        $this->observers->detach($observer);
    }

    /**
     * {@inheritDoc}
     */
    public function notify(): void
    {
        /** @var \Leevel\Event\Observer $observer */
        foreach ($this->observers as $observer) {
            $observer->update($this);
        }
    }

    /**
     * 添加一个观察者角色.
     *
     * @throws \InvalidArgumentException
     */
    public function register(\Closure|\SplObserver|string $observer): void
    {
        if ($observer instanceof \Closure) {
            $observer = new Observer($observer);
        } else {
            if (\is_string($observer)
                && \is_string($observer = $this->container->make($observer))) {
                $e = sprintf('Observer `%s` is invalid.', $observer);

                throw new \InvalidArgumentException($e);
            }

            if (!$observer instanceof \SplObserver) {
                if (!\is_callable([$observer, 'handle'])) {
                    $e = sprintf('Observer `%s` is invalid.', $observer::class);

                    throw new \InvalidArgumentException($e);
                }

                $observer = new Observer(\Closure::fromCallable([$observer, 'handle']));
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
     * 设置通知附加参数.
     */
    public function setNotifyArgs(...$args): void
    {
        $this->notifyArgs = $args;
    }

    /**
     * 获取通知附加参数.
     */
    public function getNotifyArgs(): array
    {
        return $this->notifyArgs;
    }
}
