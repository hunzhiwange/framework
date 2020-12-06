<?php

declare(strict_types=1);

namespace Tests\Router\Controllers;

class ShouldJson
{
    public function index(): array
    {
        return ['foo' => 'bar'];
    }
}
