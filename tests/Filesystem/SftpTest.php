<?php

declare(strict_types=1);

namespace Tests\Filesystem;

use League\Flysystem\Filesystem as LeagueFilesystem;
use Leevel\Filesystem\Sftp;
use Tests\TestCase;

final class SftpTest extends TestCase
{
    public function testBaseUse(): void
    {
        $this->expectException(\League\Flysystem\UnableToWriteFile::class);

        set_error_handler(function ($type, $msg): void {});
        $sftp = new Sftp();
        $this->assertInstanceof(LeagueFilesystem::class, $sftp->getFilesystem());
        $sftp->write('hello.txt', 'foo');
        restore_error_handler();
    }
}
