<?php

declare(strict_types=1);

namespace Leevel\Event;

use Leevel\Di\Provider;

/**
 * 事件服务提供者.
 */
abstract class EventProvider extends Provider
{
    /**
     * 监听器列表.
     */
    protected array $listeners = [];

    /**
     * 注册事件监听器.
     */
    public function bootstrap(IDispatch $dispatch): void
    {
        foreach ($this->get() as $event => $listeners) {
            foreach ($listeners as $key => $item) {
                if (is_int($item)) {
                    $dispatch->register($event, $key, $item);
                } else {
                    $dispatch->register($event, $item);
                }
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function register(): void
    {
    }

    /**
     * 取得监听器.
     */
    public function get(): array
    {
        return $this->listeners;
    }
}
