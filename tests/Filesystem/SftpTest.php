<?php

declare(strict_types=1);

/*
 * This file is part of the ************************ package.
 * _____________                           _______________
 *  ______/     \__  _____  ____  ______  / /_  _________
 *   ____/ __   / / / / _ \/ __`\/ / __ \/ __ \/ __ \___
 *    __/ / /  / /_/ /  __/ /  \  / /_/ / / / / /_/ /__
 *      \_\ \_/\____/\___/_/   / / .___/_/ /_/ .___/
 *         \_\                /_/_/         /_/
 *
 * The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
 * (c) 2010-2020 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
