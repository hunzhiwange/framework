<?php

declare(strict_types=1);

namespace Tests\Filesystem;

use League\Flysystem\Filesystem as LeagueFilesystem;
use Leevel\Filesystem\Local;
use Tests\TestCase;

/**
 * @internal
 */
final class LocalTest extends TestCase
{
    public function testBaseUse(): void
    {
        $local = new Local([
            'path' => $path = __DIR__,
        ]);
        $this->assertInstanceof(LeagueFilesystem::class, $local->getFilesystem());

        $local->write('hello.txt', 'foo');

        $file = $path.'/hello.txt';

        static::assertTrue(is_file($file));
        static::assertSame('foo', file_get_contents($file));
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
