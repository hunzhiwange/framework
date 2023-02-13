<?php

declare(strict_types=1);

namespace Leevel\Page;

/**
 * IRender 接口.
 */
interface IRender
{
    /**
     * 渲染.
     */
    public function render(array $option = []): string;
}
