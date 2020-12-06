<?php

declare(strict_types=1);

namespace Tests\Di\Fixtures;

class Test5 implements ITest3
{
    public $arg1;

    public $arg2;

    public function __construct(Test3 $arg1, $arg2 = 'hello default')
    {
        $this->arg1 = $arg1;
        $this->arg2 = $arg2;
    }
}
