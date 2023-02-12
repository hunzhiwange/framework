<?php

declare(strict_types=1);

namespace Leevel\Database\Ddd;

/**
 * 实体未找到异常.
 */
class EntityNotFoundException extends \RuntimeException
{
    /**
     * 实体名字.
     */
    protected string $entity = '';

    /**
     * 设置实体.
     */
    public function setEntity(string $entity): self
    {
        $this->entity = $entity;
        $this->message = sprintf('Entity `%s` was not found.', $entity);

        return $this;
    }

    /**
     * 取回实体.
     */
    public function entity(): string
    {
        return $this->entity;
    }

    /**
     * 异常是否需要上报.
     */
    public function reportable(): bool
    {
        return false;
    }
}
