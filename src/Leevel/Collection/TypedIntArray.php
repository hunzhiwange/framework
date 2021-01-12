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

        // 一般请求对象表单的数据都是字符串
        // 创建带有类型校验的集合
        $collection = new Collection($data, ['int', 'string'], ['int']);

        // 转换格式
        $data = $collection->toArray();
        $data = array_map(fn(string|int $v) => (int) $v, $data);

        return new static($data);
    }
}
