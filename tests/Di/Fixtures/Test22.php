<?php

declare(strict_types=1);

namespace Tests\Di\Fixtures;

class Test22
{
    public $prop;

    public function __construct(?string $prop = null)
    {
        $this->prop = $prop;
    }
}
