<?php

declare(strict_types=1);

namespace Leevel\Support;

/**
 * IHtml 接口.
 */
interface IHtml
{
    /**
     * 转化输出 HTML.
     */
    public function toHtml(): string;
}
