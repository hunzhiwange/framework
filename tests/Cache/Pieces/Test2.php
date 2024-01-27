<?php

declare(strict_types=1);

namespace Tests\Cache\Pieces;

use Leevel\Cache\File;
use Leevel\Cache\ICache;

class Test2
{
    public function handlew(array $params = [])
    {
        return ['hello' => 'world'];
    }

    public function cache(): ICache
    {
        return new File([
            'path' => __DIR__.'/cacheLoad',
        ]);
    }

    public static function key(array $params = []): string
    {
        return 'test2';
    }
}
