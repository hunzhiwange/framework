<?php

declare(strict_types=1);

namespace Tests\Filesystem;

use League\Flysystem\Filesystem as LeagueFilesystem;
use League\Flysystem\UnableToWriteFile;
use Leevel\Filesystem\Sftp;
use Tests\TestCase;

final class SftpTest extends TestCase
{
    public function testBaseUse(): void
    {
        $this->setInIsolation(true);
        $this->expectException(UnableToWriteFile::class);

        set_error_handler(static function ($type, $msg): void {});
        $sftp = new Sftp();
        $this->assertInstanceof(LeagueFilesystem::class, $sftp->getFilesystem());
        $sftp->getFilesystem()->write('hello.txt', 'foo');
        restore_error_handler();
    }
}
