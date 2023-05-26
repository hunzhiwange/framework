<?php

declare(strict_types=1);

namespace Leevel\Database;

/**
 * 主键或唯一键重复异常.
 *
 * 用于模拟数据库 replace.
 */
class DuplicateKeyException extends \PDOException
{
    /**
     * 唯一索引.
     */
    protected string $uniqueIndex = '';

    /**
     * 返回唯一索引.
     */
    public function getUniqueIndex(): string
    {
        return $this->uniqueIndex;
    }

    /**
     * 设置唯一索引.
     */
    public function setUniqueIndex(string $uniqueIndex): void
    {
        $this->uniqueIndex = $uniqueIndex;
    }
}
