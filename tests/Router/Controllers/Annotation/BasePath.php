<?php

declare(strict_types=1);

namespace Tests\Router\Controllers\Annotation;

/**
 * BasePath.
 */
class BasePath
{
    public function normalize(): string
    {
        return 'hello plus for basePath normalize';
    }
}
