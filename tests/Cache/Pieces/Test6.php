<?php

declare(strict_types=1);

namespace Tests\Cache\Pieces;

use Leevel\Cache\File;
use Leevel\Cache\IBlock;
use Leevel\Cache\ICache;

class Test6 implements IBlock
{
    public function handle(array $params = []): mixed
    {
        return 'hello world for test6';
    }

    public function cache(): ICache
    {
        return new File([
            'path' => __DIR__.'/cacheLoad',
        ]);
    }

    public static function key(array $params = []): string
    {
        return 'test6';
    }
}
