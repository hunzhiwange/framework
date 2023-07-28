<?php

declare(strict_types=1);

namespace Leevel\Support;

/**
 * VectorDto 动态数组是有序的、可迭代的数据传输对象数据结构.
 *
 * @see https://docs.hhvm.com/hack/arrays-and-collections/vec-keyset-and-dict
 */
final class VectorDto extends Vector
{
    /**
     * 构造函数.
     */
    public function __construct(array $data)
    {
        parent::__construct($data, Dto::class);
    }

    /**
     * 从 HTTP 请求创建数据传输对象数组.
     */
    public static function fromRequest(array $data, string $dtoClass): self
    {
        if (!is_subclass_of($dtoClass, Dto::class)) {
            throw new \InvalidArgumentException(sprintf('Dto class %s must be subclass of %s.', $dtoClass, Dto::class));
        }

        $dtoItems = [];
        foreach ($data as $item) {
            $dtoItems[] = new $dtoClass($item);
        }

        return new self($dtoItems);
    }
}
