<?php

declare(strict_types=1);

namespace Tests\Router\Controllers;

/**
 * OPTION 占位.
 */
class Options
{
    /**
     * 默认方法.
     */
    public function index(): string
    {
        return 'cors';
    }
}
