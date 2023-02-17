<?php

declare(strict_types=1);

namespace Tests\Support;

use Leevel\Support\IArray;

class TestArray implements IArray
{
    public function toArray(): array
    {
        return [
            'hello',
            'world',
        ];
    }
}
