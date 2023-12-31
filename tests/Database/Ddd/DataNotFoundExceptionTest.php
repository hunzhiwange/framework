<?php

declare(strict_types=1);

namespace Tests\Database\Ddd;

use Leevel\Database\Ddd\DataNotFoundException;
use Tests\Database\DatabaseTestCase as TestCase;

final class DataNotFoundExceptionTest extends TestCase
{
    public function test1(): void
    {
        $e = new DataNotFoundException();
        static::assertFalse($e->reportable());
    }
}
