<?php

declare(strict_types=1);

namespace Leevel\Database\Ddd;

use Leevel\Support\Collection;

/**
 * 实体集合.
 */
class EntityCollection extends Collection
{
    /**
     * 键类型.
     */
    protected array $keyTypes = ['int'];

    /**
     * 值类型.
     */
    protected array $valueTypes = [Entity::class];

    /**
     * 构造函数.
     */
    public function __construct(array $data, array $valueTypes = [])
    {
        if ($valueTypes) {
            if (1 === \count($valueTypes)
                && isset($valueTypes[0])
                && \is_string($valueTypes[0])
                && is_subclass_of($valueTypes[0], Entity::class)) {
                parent::__construct($data, $valueTypes);

                return;
            }

            throw new \InvalidArgumentException(sprintf('Value types must be a subclass of `%s`.', Entity::class));
        }

        parent::__construct($data);
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
