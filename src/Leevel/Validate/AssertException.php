<?php

declare(strict_types=1);

namespace Leevel\Validate;

/**
 * 断言异常.
 */
class AssertException extends \RuntimeException
{
    /**
     * 错误消息.
     */
    protected array $error = [];

    /**
     * 返回错误消息.
     */
    public function getError(): array
    {
        return $this->error;
    }

    /**
     * 设置错误消息.
     */
    public function setError(array $error): void
    {
        $this->error = $error;
    }

    /**
     * 异常是否需要上报.
     */
    public function reportable(): bool
    {
        return false;
    }
}
