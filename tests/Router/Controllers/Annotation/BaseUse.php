<?php

declare(strict_types=1);

namespace Tests\Router\Controllers\Annotation;

class BaseUse
{
    public function handle(): string
    {
        return 'hello plus base use';
    }
}
