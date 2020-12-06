<?php

declare(strict_types=1);

namespace Tests\Debug;

use Leevel\Debug\Helper;
use Tests\TestCase;

class HelperTest extends TestCase
{
    public function testBaseUse(): void
    {
        $this->assertSame(5, Helper::drr(5));
        $this->assertSame([0, 2], Helper::drr(0, 2));
    }

    public function testHelperNotFound(): void
    {
        $this->expectException(\Error::class);
        $this->expectExceptionMessage(
            'Call to undefined function Leevel\\Debug\\Helper\\not_found()'
        );

        $this->assertFalse(Helper::notFound());
    }
}
