<?php

declare(strict_types=1);

namespace Tests\Support;

use Leevel\Support\IJson;

class TestJson implements IJson
{
    public function toJson(?int $option = null): string
    {
        if (null === $option) {
            $option = JSON_UNESCAPED_UNICODE;
        }

        return json_encode([
            'hello',
            'world',
        ], $option);
    }
}
