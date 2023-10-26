<?php

declare(strict_types=1);

namespace Leevel\Support;

/**
 * Map 映射是有序键值数据结构.
 *
 * - 键必须是 string 或 int。
 * - 根据插入顺序排序。
 *
 * @see https://docs.hhvm.com/hack/arrays-and-collections/vec-keyset-and-dict
 */
class Map extends Collection
{
    /**
     * 构造函数.
     */
    public function __construct(array $data, string $keyType, string $valueType)
    {
        if (!\in_array($keyType, ['int', 'string'], true)) {
            throw new \InvalidArgumentException(sprintf('Key type must be int or string but `%s` given.', $keyType));
        }

        parent::__construct($data, $valueType ? [$valueType] : [], [$keyType]);
    }
}
