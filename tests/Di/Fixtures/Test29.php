<?php

declare(strict_types=1);

namespace Tests\Di\Fixtures;

class Test29
{
    private $test;

    public function __construct(TestNotFoundNotFound $test)
    {
        $this->test = $test;
    }

    public function hello()
    {
        return $this->test->hello();
    }
}
