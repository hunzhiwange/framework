<?php

declare(strict_types=1);

namespace Leevel\Collection;

/**
 * 字符串关联数组.
 */
final class TypedAssociativeStringArray extends Collection
{
    /**
     * 键类型.
     */
    protected array $keyTypes = ['string'];

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
