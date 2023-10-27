<?php

declare(strict_types=1);

namespace Tests\Support\Fixtures;

use Leevel\Support\Dto;

class Dto5 extends Dto
{
    public ?string $demoStringProp = 'hello world';

    protected function demoStringPropTransformValue(?int $value = null): string
    {
        return (string) $value;
    }
}
