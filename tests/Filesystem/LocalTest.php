<?php

declare(strict_types=1);

namespace Tests\Filesystem;

use League\Flysystem\Filesystem as LeagueFilesystem;
use Leevel\Filesystem\Local;
use Tests\TestCase;

class LocalTest extends TestCase
{
    public function testBaseUse(): void
    {
        $local = new Local([
            'path' => $path = __DIR__,
        ]);
        $this->assertInstanceof(LeagueFilesystem::class, $local->getFilesystem());

        $local->put('hello.txt', 'foo');

        $file = $path.'/hello.txt';

        $this->assertTrue(is_file($file));
        $this->assertSame('foo', file_get_contents($file));
        unlink($file);
    }

    public function testPathNotFound(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The local driver requires path option.');

        $local = new Local([
            'path' => '',
        ]);
    }
}
