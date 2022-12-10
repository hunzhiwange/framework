<?php

declare(strict_types=1);

namespace Leevel\Support;

/**
 * 关联数组.
 */
final class TypedAssociativeArray extends Collection
{
    /**
     * 键类型.
     */
    protected array $keyTypes = ['string'];

    /**
     * 构造函数.
     */
    public function __construct(array $data)
    {
        parent::__construct($data);
    }
}
