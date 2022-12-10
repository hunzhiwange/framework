<?php

declare(strict_types=1);

namespace Leevel\Support;

/**
 * 数据传输对象索引数组.
 */
class TypedDtoArray extends Collection
{
    /**
     * 键类型.
     */
    protected array $keyTypes = ['int'];

    /**
     * 值类型.
     */
    protected array $valueTypes = [Dto::class];

    /**
     * 构造函数.
     */
    public function __construct(array $data)
    {
        parent::__construct($data);
    }

    /**
     * 从 HTTP 请求创建数据传输对象数组.
     */
    public static function fromRequest(array $data, string $dtoClass): static
    {
        $dtoItems = [];
        foreach ($data as $item) {
            $dtoItems[] = new $dtoClass($item);
        }

        return new static($dtoItems);
    }
}
