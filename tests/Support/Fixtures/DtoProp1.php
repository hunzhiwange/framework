<?php

declare(strict_types=1);

namespace Tests\Support\Fixtures;

use Leevel\Support\IArray;

class DtoProp1 implements IArray
{
    public string $demo1 = 'hello';
    public string $demo2 = 'world';

    public function toArray(): array
    {
        return [
            'demo1' => $this->demo1,
            'demo2' => $this->demo2,
        ];
    }
}
