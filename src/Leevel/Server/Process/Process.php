<?php

declare(strict_types=1);

namespace Leevel\Server\Process;

use Leevel\Server\IServer;

/**
 * 进程抽象类.
 */
abstract class Process
{
    /**
     * 进程名字.
     */
    protected string $name = '';

    /**
     * 获取进程名称.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * 响应句柄.
     */
    abstract public function handle(IServer $server): void;
}
