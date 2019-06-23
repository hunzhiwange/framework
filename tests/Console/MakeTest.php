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
 * (c) 2010-2019 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Console;

use Leevel\Console\Make;
use Leevel\Filesystem\Fso;
use Tests\Console\Command\MakeFile;
use Tests\Console\Command\MakeFileWithGlobalReplace;
use Tests\TestCase;

/**
 * make test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.07.23
 *
 * @version 1.0
 */
class MakeTest extends TestCase
{
    use BaseMake;

    protected function setUp(): void
    {
        $dirs = [
            __DIR__.'/Command/cache',
            __DIR__.'/Command/cacheParentDir',
        ];

        foreach ($dirs as $dir) {
            if (is_dir($dir)) {
                Fso::deleteDirectory($dir, true);
            }
        }
    }

    public function testBaseUse(): void
    {
        $result = $this->runCommand(new MakeFile(), [
            'command'     => 'make:test',
            'name'        => 'test',
        ]);

        $this->assertStringContainsString('test <test> created successfully.', $result);

        $file = __DIR__.'/Command/cache/test';

        $this->assertStringContainsString('hello make file', $content = file_get_contents($file));

        $this->assertStringContainsString('hello key1', $content);
        $this->assertStringContainsString('hello key2', $content);
        $this->assertStringContainsString('hello key3', $content);
        $this->assertStringContainsString('hello key4', $content);

        unlink($file);
        rmdir(dirname($file));
    }

    public function testFileAleadyExists(): void
    {
        $file = __DIR__.'/Command/cache/test2';
        $dirname = dirname($file);
        mkdir($dirname, 0777, true);
        file_put_contents($file, 'foo');

        $result = $this->runCommand(new MakeFile(), [
            'command'     => 'make:test',
            'name'        => 'test2',
        ]);

        $this->assertStringContainsString('File is already exits.', $result);

        unlink($file);
        rmdir($dirname);
    }

    public function testFileIsNotWritable(): void
    {
        $file = __DIR__.'/Command/cache/test3';
        $dirname = dirname($file);
        mkdir($dirname, 0444, true);

        if (is_writable($dirname)) {
            $this->markTestSkipped('Mkdir with chmod is invalid.');
        }

        $result = $this->runCommand(new MakeFile(), [
            'command'     => 'make:test',
            'name'        => 'test3',
        ]);

        $this->assertStringContainsString('is not writeable.', $result);

        rmdir($dirname);
    }

    public function testTemplateNotFound(): void
    {
        $file = __DIR__.'/Command/cache/test4';
        $dirname = dirname($file);

        $result = $this->runCommand(new MakeFile(), [
            'command'         => 'make:test',
            'name'            => 'test4',
            'template'        => 'notFound',
        ]);

        $this->assertStringContainsString('Stub not found.', $result);
    }

    public function testFileParentDirIsNotWritable(): void
    {
        $cacheDir = __DIR__.'/Command/cacheParentDir/sub';
        $dirname = dirname($cacheDir);
        mkdir($dirname, 0444, true);

        if (is_writable($dirname)) {
            $this->markTestSkipped('Mkdir with chmod is invalid.');
        }

        $result = $this->runCommand(new MakeFile(), [
            'command'     => 'make:test',
            'name'        => 'test',
            'cache'       => 'cacheParentDir/sub',
        ]);

        $this->assertStringContainsString('is not writeable.', $result);

        rmdir($dirname);
    }

    public function testMakeFileWithGlobalReplace(): void
    {
        $result = $this->runCommand(new MakeFileWithGlobalReplace(), [
            'command'     => 'makewithglobal:test',
            'name'        => 'test',
        ]);

        $this->assertStringContainsString('test <test> created successfully.', $result);

        $file = __DIR__.'/Command/cache/test';

        $this->assertStringContainsString('hello make file', $content = file_get_contents($file));

        $this->assertStringContainsString('hello key1', $content);
        $this->assertStringContainsString('hello key2 global', $content);
        $this->assertStringContainsString('hello key3', $content);
        $this->assertStringContainsString('hello key4', $content);

        unlink($file);
        rmdir(dirname($file));
    }
}
