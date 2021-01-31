<?php

declare(strict_types=1);

namespace Leevel\Collection;

/**
 * 整数索引数组.
 */
final class TypedIntArray extends Collection
{
    /**
     * 键类型.
     */
    protected array $keyTypes = ['int'];

    /**
     * 值类型.
     */
    protected array $valueTypes = ['int'];

    /**
     * 构造函数.
     */
    public function __construct(array $data)
    {
        parent::__construct($data);
    }

    /**
     * 从 HTTP 请求创建整数索引数组.
     */
    public static function fromRequest(string|array $data): static
    {
        if (is_string($data)) {
            $data = explode(',', $data);
        }

        array_walk(
            $data, 
            fn(int|string &$value, int $key) => $value = (int) $value,
        );

        return new static($data);
    }
}
