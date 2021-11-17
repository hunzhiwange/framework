<?php

declare(strict_types=1);

namespace Leevel\Protocol\Process;

/**
 * Swoole 自动进程抽象类.
 */
abstract class Process
{
    /**
     * 进程名字.
     */
    protected string $name;

    /**
     * 获取进程名称.
     */
    public function getName(): string
    {
        return $this->name;
    }
}
