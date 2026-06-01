<?php

declare(strict_types=1);

namespace Tests\Support\Fixtures;

use Leevel\Support\Dto;

class Dto6 extends Dto
{
    public int $int1 = 0 {
        set(int|float|null $v) {
            $this->int1 = $this->set('int1', $v);
        }
    }

    public string $string2 = '';

    public float $float3 = 0.0;

    public bool $bool4 = true;

    public array $array5 = [];

    public ?int $helloInt = null;
}
