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
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Filesystem;

use Leevel\Filesystem\Fso;
use Tests\TestCase;

/**
 * fso test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.07.16
 *
 * @version 1.0
 */
class FsoTest extends TestCase
{
    public function testBaseUse()
    {
        $file = __DIR__.'/hello.txt';

        file_put_contents($file, 'foo');

        $this->assertSame('foo', Fso::fileContents($file));

        unlink($file);

        $this->expectException(\RuntimeException::class);

        Fso::fileContents($file);
    }

    public function testCreateDirectory()
    {
        $dir = __DIR__.'/createDirectory';

        $this->assertDirectoryNotExists($dir);

        $this->assertTrue(Fso::createDirectory($dir));

        $this->assertDirectoryExists($dir);

        $this->assertTrue(Fso::createDirectory($dir));

        rmdir($dir);
    }

    public function testDeleteDirectory()
    {
        $dir = __DIR__.'/deleteDirectory/dir';

        $this->assertDirectoryNotExists($dir);

        Fso::deleteDirectory($dir);

        $this->assertTrue(Fso::createDirectory($dir));

        $this->assertDirectoryExists($dir);

        Fso::deleteDirectory($dir);

        $topDir = dirname($dir);

        $this->assertDirectoryExists($topDir);

        Fso::deleteDirectory($topDir);

        $this->assertDirectoryNotExists($topDir);
    }

    public function testDeleteDirectory2()
    {
        $dir = __DIR__.'/deleteDirectory2/dir';

        $topDir = dirname($dir);

        $this->assertDirectoryNotExists($dir);

        $this->assertTrue(Fso::createDirectory($dir));

        $this->assertDirectoryExists($dir);

        Fso::deleteDirectory($topDir, true);

        $this->assertDirectoryNotExists($topDir);
    }

    public function testCopyDirectory()
    {
        $sourcePath = __DIR__.'/copyDirectory';
        $sourceSubPath = __DIR__.'/CopyDirectory/dir';
        $targetPath = __DIR__.'/targetCopyDirectory';

        $this->assertDirectoryNotExists($sourceSubPath);
        $this->assertDirectoryNotExists($targetPath);

        $this->assertTrue(Fso::createDirectory($sourceSubPath));

        file_put_contents($testFile = $sourceSubPath.'/hello.txt', 'foo');

        $this->assertTrue(is_file($testFile));

        $this->assertSame('foo', file_get_contents($testFile));

        Fso::copyDirectory($sourcePath, $targetPath);

        $this->assertDirectoryExists($targetPath);
        $this->assertDirectoryExists($targetPath.'/dir');
        $this->assertTrue(is_file($targetPath.'/dir/hello.txt'));

        Fso::deleteDirectory($sourcePath, true);
        Fso::deleteDirectory($targetPath, true);
    }

    public function testCopyDirectory2()
    {
        $sourcePath = __DIR__.'/copyDirectory2';
        $sourceSubPath = __DIR__.'/copyDirectory2/dir';
        $targetPath = __DIR__.'/targetCopyDirectory2';

        $this->assertDirectoryNotExists($sourceSubPath);
        $this->assertDirectoryNotExists($targetPath);

        $this->assertTrue(Fso::createDirectory($sourceSubPath));

        file_put_contents($testFile = $sourceSubPath.'/hello.txt', 'foo');

        $this->assertTrue(is_file($testFile));

        $this->assertSame('foo', file_get_contents($testFile));

        Fso::copyDirectory($sourcePath, $targetPath, ['hello.txt']);

        $this->assertDirectoryExists($targetPath);
        $this->assertDirectoryExists($targetPath.'/dir');
        $this->assertFalse(is_file($targetPath.'/dir/hello.txt'));

        Fso::deleteDirectory($sourcePath, true);
        Fso::deleteDirectory($targetPath, true);
    }

    public function testCopyDirectory3()
    {
        $sourcePath = __DIR__.'/copyDirectory3';
        $sourceSubPath = __DIR__.'/CopyDirectory3/dir';
        $targetPath = __DIR__.'/targetCopyDirectory3';

        $this->assertDirectoryNotExists($sourceSubPath);

        Fso::copyDirectory($sourcePath, $targetPath);
    }

    public function testListDirectory()
    {
        $sourcePath = __DIR__.'/listDirectory';
        $sourceSubPath = __DIR__.'/listDirectory/dir';

        $this->assertDirectoryNotExists($sourceSubPath);

        $this->assertTrue(Fso::createDirectory($sourceSubPath));

        file_put_contents($testFile = $sourceSubPath.'/hello.txt', 'foo');

        $this->assertTrue(is_file($testFile));

        $this->assertSame('foo', file_get_contents($testFile));

        $filesAndDirs = [];
        $filesAndDirs2 = [];

        Fso::listDirectory($sourcePath, true, function ($item) use (&$filesAndDirs) {
            $filesAndDirs[] = $item->getFileName();
        });

        Fso::listDirectory($sourcePath, true, function ($item) use (&$filesAndDirs2) {
            $filesAndDirs2[] = $item->getFileName();
        }, ['hello.txt']);

        $this->assertSame(['dir', 'hello.txt'], $filesAndDirs);
        $this->assertSame(['dir'], $filesAndDirs2);

        Fso::deleteDirectory($sourcePath, true);
    }

    public function testListDirectory2()
    {
        $sourcePath = __DIR__.'/listDirectory2';

        $this->assertDirectoryNotExists($sourcePath);

        Fso::listDirectory($sourcePath, true, function ($item) {
        });
    }

    public function testTidyPath()
    {
        $sourcePath = '/home\goods/name/';

        $this->assertSame('/home/goods/name', Fso::tidyPath($sourcePath));
        $this->assertSame('\home\goods\name', Fso::tidyPath($sourcePath, false));
    }

    public function testDistributed()
    {
        $this->assertSame(['000/00/00/', '01'], Fso::distributed(1));

        $this->assertSame(['090/00/00/', '00'], Fso::distributed(90000000));
    }

    public function testCreateFile()
    {
        $sourcePath = __DIR__.'/createFile';
        $file = $sourcePath.'/hello.txt';

        $this->assertDirectoryNotExists($sourcePath);

        $this->assertTrue(Fso::createDirectory($sourcePath));

        $this->assertFalse(is_file($file));

        Fso::createFile($file);

        $this->assertTrue(is_file($file));

        Fso::deleteDirectory($sourcePath, true);
    }

    public function testCreateFile2()
    {
        $file = __DIR__.'/FsoTest.php';

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Dir can not be a file.'
        );

        Fso::createFile($file.'/demo.txt');
    }

    public function testGetExtension()
    {
        $file = __DIR__.'/FsoTest.pHp';

        $this->assertSame('pHp', Fso::getExtension($file));
        $this->assertSame('PHP', Fso::getExtension($file, 1));
        $this->assertSame('php', Fso::getExtension($file, 2));
    }

    public function testGetName()
    {
        $file = __DIR__.'/FsoTest.pHp';

        $this->assertSame('FsoTest', Fso::getName($file));
    }
}
