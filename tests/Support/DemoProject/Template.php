<?php

declare(strict_types=1);

namespace Tests\Support\DemoProject;

use Leevel\Support\Dto;
use Leevel\Support\VectorDto;

class Template extends Dto
{
    public string $key;

    public string $title;

    public VectorDto $data;
}
