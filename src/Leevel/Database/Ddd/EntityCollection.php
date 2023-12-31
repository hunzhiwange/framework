<?php

declare(strict_types=1);

namespace Leevel\Database\Ddd;

use Leevel\Support\Vector;

/**
 * 实体集合.
 */
class EntityCollection extends Vector
{
    /**
     * 值类型.
     */
    protected array $valueTypes = [Entity::class];

    /**
     * 构造函数.
     */
    public function __construct(array $data, ?string $valueType = null)
    {
        if (!$valueType || Entity::class === $valueType) {
            parent::__construct($data);

            return;
        }

        if (is_subclass_of($valueType, Entity::class)) {
            parent::__construct($data, $valueType);

            return;
        }

        throw new \InvalidArgumentException(sprintf('Value types `%s` must be a subclass of `%s`.', $valueType, Entity::class));
    }

    /**
     * 获取实体.
     *
     * @throws \Exception
     */
    public function get(int $key): Entity
    {
        $entity = $this->__get($key);
        if (!$entity) {
            throw new \Exception(sprintf('Entity %d was not found.', $key));
        }

        // @phpstan-ignore-next-line
        return $entity;
    }

    /**
     * 设置实体.
     */
    public function set(int $key, Entity $entity): void
    {
        $this->__set($key, $entity);
    }

    /**
     * 删除实体.
     */
    public function remove(int $key): void
    {
        $this->__unset($key);
    }

    /**
     * 是否存在实体.
     */
    public function has(int $key): bool
    {
        return $this->__isset($key);
    }
}
