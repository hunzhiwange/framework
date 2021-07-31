<?php

declare(strict_types=1);

namespace Tests\Support\Fixtures;

use Leevel\Support\Dto;

class DtoToArray2 extends Dto
{
    public string $demoStringProp;

    public int $demoIntProp;

    public int|string $demoIntOrStringProp;

    protected array $exceptPropertys = [
        'demoIntProp', 'demoIntOrStringProp'
    ];
}
