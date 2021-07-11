<?php

declare(strict_types=1);

namespace Leevel\Event;

use Closure;
use InvalidArgumentException;
use SplObserver;
use SplSubject;

/**
 * 观察者角色 observer.
 *
 * @see http://php.net/manual/zh/class.splobserver.php
 */
class Observer implements SplObserver
{
    /**
     * 观察者实现.
    */
    protected ?Closure $handle = null;

    /**
     * 构造函数.
     */
    public function __construct(?Closure $handle = null)
    {
        $this->handle = $handle;
    }

    /**
     * {@inheritDoc}
     *
     * @throws \InvalidArgumentException
     */
    public function update(SplSubject $subject): void
    {
        $handle = null;
        if (method_exists($this, 'handle')) {
            $handle = [$this, 'handle'];
        } elseif ($this->handle) {
            $handle = $this->handle;
        }

        if (!is_callable($handle)) {
            $e = sprintf('Observer %s must has handle method.', $this::class);

            throw new InvalidArgumentException($e);
        }

        $subject = $this->convertSubject($subject);
        $subject->getContainer()->call($handle, $subject->getNotifyArgs());
    }

    /**
     * 转换观察者目标角色.
     * 
     * - For PHPStan
     */
    protected function convertSubject(SplSubject $subject): Subject
    {
        return $subject;
    }
}
