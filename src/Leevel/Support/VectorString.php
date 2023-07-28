<?php

declare(strict_types=1);

namespace Leevel\Support;

/**
 * VectorString 动态数组是有序的、可迭代的字符串数据结构.
 *
 * @see https://docs.hhvm.com/hack/arrays-and-collections/vec-keyset-and-dict
 */
final class VectorString extends Vector
{
    /**
     * 构造函数.
     */
    public function __construct(array $data)
    {
        parent::__construct($data, 'string');
    }

    /**
     * 从 HTTP 请求创建 VecString 数组.
     */
    public static function fromRequest(string|array $data): self
    {
        if (\is_string($data)) {
            $data = explode(',', $data);
        }

        array_walk(
            $data,
            fn (int|float|string & $value, int $key) => $value = (string) $value,
        );

        return new self($data);
    }
}
