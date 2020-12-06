<?php

declare(strict_types=1);

namespace Tests\Router\Controllers\Annotation;

class BindMethodNotFound
{
    public function notFound(): string
    {
        return 'bind method not found';
    }
}
