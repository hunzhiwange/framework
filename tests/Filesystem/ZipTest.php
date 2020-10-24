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
use Leevel\Filesystem\Zip;
use Tests\TestCase;

class ZipTest extends TestCase
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

        $zip->put('hello.txt', 'foo');
    }

    public function testPathNotFound(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The zip driver requires path option.');

        $zip = new zip([
            'path' => '',
        ]);
    }
}
