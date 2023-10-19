<?php

declare(strict_types=1);

namespace Tests\Filesystem;

use League\Flysystem\Filesystem as LeagueFilesystem;
use Leevel\Filesystem\Ftp;
use Tests\TestCase;

final class FtpTest extends TestCase
{
    public function testBaseUse(): void
    {
        $this->expectException(\League\Flysystem\Ftp\UnableToConnectToFtpHost::class);

        $ftp = new Ftp();
        $this->assertInstanceof(LeagueFilesystem::class, $ftp->getFilesystem());
        $ftp->write('hello.txt', 'foo');
    }
}
