<?php

declare(strict_types=1);

namespace Tests\Di\Fixtures;

class Test10
{
    public function hello(TestNotFound $test)
    {
        return 'test10';
    }
}
