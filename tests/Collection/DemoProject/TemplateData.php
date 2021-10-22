<?php

declare(strict_types=1);

namespace Tests\Collection\DemoProject;

use Leevel\Support\Dto;

class TemplateData extends Dto
{
    public string $title;

    public string $tag;

    public string $description;
}
