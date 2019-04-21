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

namespace Tests\Option\Console;

use Leevel\Di\IContainer;
use Leevel\Filesystem\Fso;
use Leevel\Kernel\IApp;
use Leevel\Option\Console\Cache;
use Tests\Console\BaseCommand;
use Tests\TestCase;

/**
 * cache test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.07.17
 *
 * @version 1.0
 */
class CacheTest extends TestCase
{
    use BaseCommand;

    protected function setUp(): void
    {
        $dirs = [
            __DIR__.'/dirWriteable',
            __DIR__.'/parentDirWriteable',
            __DIR__.'/dirNotExists',
            __DIR__.'/assertRelative/relative',
        ];

        foreach ($dirs as $dir) {
            if (is_dir($dir)) {
                Fso::deleteDirectory($dir, true);
            }
        }

        $file = __DIR__.'/option_cache.php';

        if (is_file($file)) {
            unlink($file);
        }
    }

    protected function tearDown(): void
    {
        $this->setUp();
    }

    public function testBaseUse()
    {
        $cacheFile = __DIR__.'/option_cache.php';

        $result = $this->runCommand(
            new Cache(),
            [
                'command' => 'option:cache',
            ],
            function ($container) use ($cacheFile) {
                $this->initContainerService($container, $cacheFile);
            }
        );

        $result = $this->normalizeContent($result);

        $this->assertStringContainsString(
            $this->normalizeContent('Start to cache option.'),
            $result
        );

        $this->assertStringContainsString(
            $this->normalizeContent(sprintf('Option cache file %s cache successed.', $cacheFile)),
            $result
        );

        $optionData = file_get_contents(__DIR__.'/assert/option.json');

        $this->assertSame(
            trim($optionData),
            $this->varJson(
                (array) (include $cacheFile)
            )
        );

        unlink($cacheFile);
    }

    public function testDirNotExists()
    {
        $cacheFile = __DIR__.'/dirNotExists/option_cache.php';

        $result = $this->runCommand(
            new Cache(),
            [
                'command' => 'option:cache',
            ],
            function ($container) use ($cacheFile) {
                $this->initContainerService($container, $cacheFile);
            }
        );

        $result = $this->normalizeContent($result);

        $this->assertStringContainsString(
            $this->normalizeContent('Start to cache option.'),
            $result
        );

        $this->assertStringContainsString(
            $this->normalizeContent(sprintf('Option cache file %s cache successed.', $cacheFile)),
            $result
        );

        $optionData = file_get_contents(__DIR__.'/assert/option.json');

        $this->assertSame(
            trim($optionData),
            $this->varJson(
                (array) (include $cacheFile)
            )
        );

        unlink($cacheFile);
        rmdir(dirname($cacheFile));
    }

    public function testDirRelative()
    {
        $cacheFile = __DIR__.'/assertRelative/relative/k/option_cache.php';

        $result = $this->runCommand(
            new Cache(),
            [
                'command' => 'option:cache',
            ],
            function ($container) use ($cacheFile) {
                $this->initContainerService($container, $cacheFile, 'assertRelative');
            }
        );

        $result = $this->normalizeContent($result);

        $this->assertStringContainsString(
            $this->normalizeContent('Start to cache option.'),
            $result
        );

        $this->assertStringContainsString(
            $this->normalizeContent(sprintf('Option cache file %s cache successed.', $cacheFile)),
            $result
        );

        $optionData = (array) (include __DIR__.'/assertRelative/option.php');

        $this->assertSame(
            $optionData,
            (array) (include $cacheFile)
        );

        unlink($cacheFile);
        rmdir(dirname($cacheFile));
    }

    public function testDirWriteable()
    {
        $dirname = __DIR__.'/dirWriteable';
        $cacheFile = $dirname.'/option_cache.php';

        $optionData = [
            'foo'   => 'bar',
            'hello' => 'world',
        ];

        // 设置目录只读
        // 7 = 4+2+1 分别代表可读可写可执行
        mkdir($dirname, 0444);

        if (is_writable($dirname)) {
            $this->markTestSkipped('Mkdir with chmod is invalid.');
        }

        $this->assertDirectoryExists($dirname);

        $result = $this->runCommand(
            new Cache(),
            [
                'command' => 'option:cache',
            ],
            function ($container) use ($cacheFile) {
                $this->initContainerService($container, $cacheFile);
            }
        );

        $result = $this->normalizeContent($result);

        $this->assertStringContainsString(
            $this->normalizeContent('Start to cache option.'),
            $result
        );

        $this->assertStringContainsString(
            $this->normalizeContent(sprintf('Dir %s is not writeable.', $dirname)),
            $result
        );

        rmdir($dirname);
    }

    public function testParentDirWriteable()
    {
        $dirname = __DIR__.'/parentDirWriteable/sub';
        $cacheFile = $dirname.'/option_cache.php';

        $optionData = [
            'foo'   => 'bar',
            'hello' => 'world',
        ];

        // 设置目录只读
        // 7 = 4+2+1 分别代表可读可写可执行
        mkdir(dirname($dirname), 0444);

        if (is_writable(dirname($dirname))) {
            $this->markTestSkipped('Mkdir with chmod is invalid.');
        }

        $this->assertDirectoryExists(dirname($dirname));

        $this->assertDirectoryNotExists($dirname);

        $result = $this->runCommand(
            new Cache(),
            [
                'command' => 'option:cache',
            ],
            function ($container) use ($cacheFile, $optionData) {
                $this->initContainerService($container, $cacheFile);
            }
        );

        $result = $this->normalizeContent($result);

        $this->assertStringContainsString(
            $this->normalizeContent('Start to cache option.'),
            $result
        );

        $this->assertStringContainsString(
            $this->normalizeContent(sprintf('Unable to create the %s directory.', $dirname)),
            $result
        );

        rmdir(dirname($dirname));
    }

    protected function initContainerService(IContainer $container, string $cacheFile, string $assertDir = 'assert')
    {
        // 注册 app
        $app = $this->createMock(IApp::class);

        $this->assertInstanceof(IApp::class, $app);

        $app->method('path')->willReturn(__DIR__.'/'.$assertDir);
        $this->assertEquals(__DIR__.'/'.$assertDir, $app->path());

        $app->method('envPath')->willReturn(__DIR__.'/'.$assertDir);
        $this->assertEquals(__DIR__.'/'.$assertDir, $app->envPath());

        $app->method('envFile')->willReturn('.env');
        $this->assertEquals('.env', $app->envFile());

        $app->method('optionCachedPath')->willReturn($cacheFile);
        $this->assertEquals($cacheFile, $app->optionCachedPath());

        $app->method('optionPath')->willReturn(__DIR__.'/'.$assertDir.'/option');
        $this->assertEquals(__DIR__.'/'.$assertDir.'/option', $app->optionPath());

        $container->singleton(IApp::class, $app);
    }
}
