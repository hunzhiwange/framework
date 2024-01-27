<?php

declare(strict_types=1);

namespace Leevel\Support;

/**
 * IJson 接口.
 */
interface IJson
{
    /**
     * 对象转 JSON.
     */
    public function toJson(?int $config = null): string;
}
