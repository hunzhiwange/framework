<?php

declare(strict_types=1);

namespace Leevel\Level\Provider;

use Leevel\Di\IContainer;
use Leevel\Di\ICoroutine;
use Leevel\Di\Provider;
use Leevel\Level\Coroutine;
use Leevel\Level\HttpServer;
use Leevel\Level\ITask;
use Leevel\Level\ITimer;
use Leevel\Level\Task;
use Leevel\Level\Timer;
use Leevel\Level\WebsocketServer;

/**
 * Swoole 服务提供者.
 */
class Register extends Provider
{
    /**
     * {@inheritDoc}
     */
    public function register(): void
    {
        $this->coroutine();
        $this->httpServer();
        $this->websocketServer();
        $this->task();
        $this->timer();
    }

    /**
     * {@inheritDoc}
     */
    public static function providers(): array
    {
        return [
            'coroutine'        => [ICoroutine::class, Coroutine::class],
            'http.server'      => HttpServer::class,
            'websocket.server' => WebsocketServer::class,
            'task'             => [ITask::class, Task::class],
            'timer'            => [ITimer::class, Timer::class],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public static function isDeferred(): bool
    {
        return true;
    }

    /**
     * 注册 coroutine 服务.
     */
    protected function coroutine(): void
    {
        $this->container
            ->singleton(
                'coroutine',
                fn (): Coroutine => new Coroutine(),
            );
    }

    /**
     * 注册 http.server 服务.
     */
    protected function httpServer(): void
    {
        $this->container
            ->singleton(
                'http.server',
                function (IContainer $container): HttpServer {
                    return new HttpServer(
                        $container,
                        $container->make('coroutine'),
                        $this->normalizeOptions($container, 'http'),
                    );
                },
            );
    }

    /**
     * 注册 websocket.server 服务.
     */
    protected function websocketServer(): void
    {
        $this->container
            ->singleton(
                'websocket.server',
                function (IContainer $container): WebsocketServer {
                    return new WebsocketServer(
                        $container,
                        $container->make('coroutine'),
                        $this->normalizeOptions($container, 'websocket'),
                    );
                },
            );
    }

    /**
     * 注册 task 服务.
     */
    protected function task(): void
    {
        $this->container
            ->singleton(
                'task',
                fn (IContainer $container): Task => new Task($container->make('server')),
            );
    }

    /**
     * 注册 timer 服务.
     */
    protected function timer(): void
    {
        $this->container
            ->singleton(
                'timer',
                fn (IContainer $container): Timer => new Timer($container->make('logs')),
            );
    }

    /**
     * 整理服务配置.
     */
    protected function normalizeOptions(IContainer $container, string $serverType): array
    {
        /** @var \Leevel\Option\IOption $option */
        $option = $container->make('option');
        $options = array_merge(
            $option->get('level\\server'),
            $option->get('level\\'.$serverType)
        );
        $options = $this->mergeOptionsForProcesses($container, $options);

        return $options;
    }

    /**
     * 合并自定义进程配置.
     */
    protected function mergeOptionsForProcesses(IContainer $container, array $options): array
    {
        /** @var \Leevel\Kernel\IApp $app */
        $app = $container->make('app');

        if ($app->isDevelopment() &&
            isset($options['processes'], $options['processes_dev'])) {
            $options['processes'] = array_merge($options['processes'], $options['processes_dev']);
        }

        return $options;
    }
}
