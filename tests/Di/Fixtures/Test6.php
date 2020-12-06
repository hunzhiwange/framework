<?php

declare(strict_types=1);

namespace Tests\Di\Fixtures;

class Test6
{
    public $arg1;

    public $arg2;

    public $arg3;

    public function __construct($arg1, $arg2, $arg3)
    {
        $this->arg1 = $arg1;
        $this->arg2 = $arg2;
        $this->arg3 = $arg3;
    }
}
