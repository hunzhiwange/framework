<?php

declare(strict_types=1);

namespace Leevel\Support;

/**
 * VectorInt 动态数组是有序的、可迭代的整型数据结构.
 *
 * @see https://docs.hhvm.com/hack/arrays-and-collections/vec-keyset-and-dict
 */
final class VectorInt extends Vector
{
    /**
     * 构造函数.
     */
    public function __construct(array $data)
    {
        parent::__construct($data, 'int');
    }

    /**
     * 从 HTTP 请求创建 VecInt 数组.
     */
    public static function fromRequest(string|array $data): self
    {
        if (\is_string($data)) {
            $data = explode(',', $data);
        }

        array_walk(
            $data,
            fn (int|float|string & $value, int $key) => $value = (int) $value,
        );

        return new self($data);
    }
}
