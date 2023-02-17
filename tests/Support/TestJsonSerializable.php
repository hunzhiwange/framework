<?php

declare(strict_types=1);

namespace Tests\Support;

class TestJsonSerializable implements \JsonSerializable
{
    public function jsonSerialize(): mixed
    {
        return [
            'hello',
            'world',
        ];
    }
}
