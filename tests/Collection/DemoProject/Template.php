<?php

declare(strict_types=1);

namespace Tests\Collection\DemoProject;

use Leevel\Collection\TypedDtoArray;
use Leevel\Support\Dto;

class Template extends Dto
{
    public string $key;

    public string $title;

    public TypedDtoArray $data;
}
