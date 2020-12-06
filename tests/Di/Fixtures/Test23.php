<?php

declare(strict_types=1);

namespace Tests\Di\Fixtures;

class Test23
{
    public function handle(Test24 $arg1, Test25 $arg2, string $arg3)
    {
        return ['test24' => $arg1->prop, 'test25' => $arg2->prop, 'three' => $arg3];
    }
}
