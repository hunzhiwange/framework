<?php

declare(strict_types=1);

namespace Tests\Di\Fixtures;

class Test20
{
    public function handle(Test21 $arg1, Test22 $arg2)
    {
        return ['test21' => $arg1->prop, 'test22' => $arg2->prop];
    }
}
