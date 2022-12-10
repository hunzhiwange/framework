<?php

declare(strict_types=1);

namespace Leevel\Support;

/**
 * 字符串索引数组.
 */
final class TypedStringArray extends Collection
{
    /**
     * 键类型.
     */
    protected array $keyTypes = ['int'];

    /**
     * 值类型.
     */
    protected array $valueTypes = ['string'];

    /**
     * 构造函数.
     */
    public function __construct(array $data)
    {
        parent::__construct($data);
    }
}
