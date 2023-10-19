<?php

declare(strict_types=1);

namespace Tests\Database\Ddd;

use Leevel\Database\Ddd\EntityNotFoundException;
use Tests\Database\DatabaseTestCase as TestCase;
use Tests\Database\Ddd\Entity\Guestbook;

final class EntityNotFoundExceptionTest extends TestCase
{
    public function testBaseUse(): void
    {
        $e = new EntityNotFoundException();
        $e->setEntity(Guestbook::class);

        static::assertSame(Guestbook::class, $e->entity());
        static::assertSame('Entity `Tests\\Database\\Ddd\\Entity\\Guestbook` was not found.', $e->getMessage());
    }

    public function testEntityNotFoundExceptionReportable(): void
    {
        $e = new EntityNotFoundException();
        static::assertFalse($e->reportable());
    }
}
