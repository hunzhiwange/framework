<?php

declare(strict_types=1);

namespace Tests\Filesystem;

use League\Flysystem\Filesystem as LeagueFilesystem;
use Leevel\Filesystem\Ftp;
use Tests\TestCase;

class FtpTest extends TestCase
{
    public function testBaseUse(): void
    {
        $this->expectException(\League\Flysystem\ConnectionRuntimeException::class);
        $this->expectExceptionMessage(
            'Could not connect to host: ftp.example.com, port:21'
        );

        $ftp = new Ftp();
        $this->assertInstanceof(LeagueFilesystem::class, $ftp->getFilesystem());
        $ftp->put('hello.txt', 'foo');
    }
}
