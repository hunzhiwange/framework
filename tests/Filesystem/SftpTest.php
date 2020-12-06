<?php

declare(strict_types=1);

namespace Tests\Filesystem;

use League\Flysystem\Filesystem as LeagueFilesystem;
use Leevel\Filesystem\Sftp;
use Tests\TestCase;

class SftpTest extends TestCase
{
    public function testBaseUse(): void
    {
        $this->expectException(\League\Flysystem\Sftp\ConnectionErrorException::class);
        $this->expectExceptionMessage(
            'Could not login with username: your-username, host: sftp.example.com'
        );

        set_error_handler(function ($type, $msg) {});
        $sftp = new Sftp();
        $this->assertInstanceof(LeagueFilesystem::class, $sftp->getFilesystem());
        $sftp->put('hello.txt', 'foo');
        restore_error_handler();
    }
}
