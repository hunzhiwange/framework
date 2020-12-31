<?php

declare(strict_types=1);

namespace Leevel\Kernel\Exceptions;

use RuntimeException;

/**
 * 业务操作异常.
 *
 * - 业务异常与系统异常不同，一般不需要捕捉写入日志.
 */
abstract class BusinessException extends RuntimeException
{
    /**
     * 默认 0 表示不是很重要的业务日志.
     */
    const DEFAULT_LEVEL = 0;

    /**
     * 业务逻辑异常重要程度.
     *
     * - 不同重要程度的业务需要针对性处理日志.
     * - 默认 0 表示不是很重要的业务日志.
     */
    protected int $importance = self::DEFAULT_LEVEL;

    /**
     * 构造函数.
     */
    public function __construct(?string $message = null, int $code = 0, int $importance = self::DEFAULT_LEVEL)
    {
        parent::__construct($message, $code);
        $this->importance = $importance;
    }

    /**
     * 设置业务逻辑异常重要性.
     */
    public function setImportance(int $importance): void
    {
        $this->importance = $importance;
    }

    /**
     * 返回业务逻辑异常重要性.
     */
    public function getImportance(): int
    {
        return $this->importance;
    }
}