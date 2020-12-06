<?php

declare(strict_types=1);

namespace Tests\Di\Fixtures;

class Test3 implements ITest3
{
    public $arg1;

    public function __construct(ITest2 $arg1)
    {
        $this->arg1 = $arg1;
    }
}
