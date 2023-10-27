<?php

declare(strict_types=1);

namespace Tests\Support\Fixtures;

use Leevel\Support\Dto;

class Dto3 extends Dto
{
    public string $demoStringProp;

    protected function demoStringPropDefaultValue(): string
    {
        return 'hello world';
    }
}
