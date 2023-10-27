<?php

declare(strict_types=1);

namespace Tests\Support\Fixtures;

use Leevel\Support\Dto;

class Dto4 extends Dto
{
    public string $demoStringProp;

    protected function demoStringPropTransformValue(int $value): string
    {
        return (string) $value;
    }
}
