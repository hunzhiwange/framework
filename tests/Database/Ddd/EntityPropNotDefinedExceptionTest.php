<?php

declare(strict_types=1);

namespace Tests\Database\Ddd;

use Leevel\Database\Ddd\EntityPropNotDefinedException;
use Tests\Database\DatabaseTestCase as TestCase;

final class EntityPropNotDefinedExceptionTest extends TestCase
{
    public function test1(): void
    {
        $e = new EntityPropNotDefinedException();
        static::assertFalse($e->reportable());
    }
}
