<?php

declare(strict_types=1);

namespace Tests\Database\Ddd;

use Leevel\Database\Ddd\EntityIdentifyConditionException;
use Tests\Database\DatabaseTestCase as TestCase;

final class EntityIdentifyConditionExceptionTest extends TestCase
{
    public function test1(): void
    {
        $e = new EntityIdentifyConditionException();
        self::assertFalse($e->reportable());
    }
}
