<?php

declare(strict_types=1);

namespace Tests\Router\Controllers\Response;

class StringableResponse
{
    public function handle(): String1
    {
        return new String1();
    }
}

class String1
{
    public function __toString()
    {
        return 'stringable test.';
    }
}
