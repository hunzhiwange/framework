<?php

declare(strict_types=1);

namespace Tests\Di\Fixtures;

class Test28
{
    private $test;

    public function __construct(Test27 $test)
    {
        $this->test = $test;
    }

    public function hello()
    {
        return $this->test->hello();
    }
}
