<?php

declare(strict_types=1);

namespace Tests\Filesystem;

use League\Flysystem\Filesystem as LeagueFilesystem;
use Leevel\Filesystem\Zip;
use Tests\TestCase;

final class ZipTest extends TestCase
{
    protected function setUp(): void
    {
        $file = __DIR__.'/hello.zip';
        if (is_file($file)) {
            unlink($file);
        }
    }

    protected function tearDown(): void
    {
        $this->setUp();
    }

    public function testBaseUse(): void
    {
        $zip = new Zip([
            'path' => $path = __DIR__.'/hello.zip',
        ]);
        $this->assertInstanceof(LeagueFilesystem::class, $zip->getFilesystem());

        $zip->write('hello.txt', 'foo');
    }

    public function testPathNotFound(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The zip driver requires path config.');

        $zip = new zip([
            'path' => '',
        ]);
    }
}
