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

namespace Tests\Option\Console;

use Leevel\Kernel\IProject;
use Leevel\Option\Console\Cache;
use Leevel\Support\Facade;
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

    public function testBaseUse()
    {
        $cacheFile = __DIR__.'/option_cache.php';

        $result = $this->runCommand(
            new Cache(),
            [
                'command'     => 'option:cache',
            ],
            function ($container) use ($cacheFile) {
                // 静态属性会保持住，可能受到其它单元测试的影响
                Facade::remove('option');

                $container->singleton('option', function () {
                    return new OptionService();
                });

                $project = $this->createMock(IProject::class);

                $this->assertInstanceof(IProject::class, $project);

                $project->method('pathCacheOptionFile')->willReturn($cacheFile);
                $this->assertEquals($cacheFile, $project->pathCacheOptionFile());

                $container->singleton(IProject::class, $project);
            }
        );

        $this->assertContains(sprintf('Option file %s cache successed.', $cacheFile), $result);

        $this->assertSame(['foo' => 'bar', 'hello' => 'world'], (array) (include $cacheFile));

        unlink($cacheFile);
    }

    public function testExists()
    {
        $cacheFile = __DIR__.'/option_cache2.php';

        file_put_contents($cacheFile, 'hello');

        $this->assertTrue(is_file($cacheFile));
        $this->assertSame('hello', file_get_contents($cacheFile));

        $result = $this->runCommand(
            new Cache(),
            [
                'command'     => 'option:cache',
            ],
            function ($container) use ($cacheFile) {
                // 静态属性会保持住，可能受到其它单元测试的影响
                Facade::remove('option');

                $container->singleton('option', function () {
                    return new OptionService();
                });

                $project = $this->createMock(IProject::class);

                $this->assertInstanceof(IProject::class, $project);

                $project->method('pathCacheOptionFile')->willReturn($cacheFile);
                $this->assertEquals($cacheFile, $project->pathCacheOptionFile());

                $container->singleton(IProject::class, $project);
            }
        );

        $this->assertContains(sprintf('Option file %s cache successed.', $cacheFile), $result);

        // 如果换成文件存在，则包含警告信息
        $this->assertContains(sprintf('Option cache file %s is already exits.', $cacheFile), $result);

        $this->assertSame(['foo' => 'bar', 'hello' => 'world'], (array) (include $cacheFile));

        unlink($cacheFile);
    }

    public function testDirNotExists()
    {
        $cacheFile = __DIR__.'/dirNotExists/option_cache.php';

        $result = $this->runCommand(
            new Cache(),
            [
                'command'     => 'option:cache',
            ],
            function ($container) use ($cacheFile) {
                // 静态属性会保持住，可能受到其它单元测试的影响
                Facade::remove('option');

                $container->singleton('option', function () {
                    return new OptionService();
                });

                $project = $this->createMock(IProject::class);

                $this->assertInstanceof(IProject::class, $project);

                $project->method('pathCacheOptionFile')->willReturn($cacheFile);
                $this->assertEquals($cacheFile, $project->pathCacheOptionFile());

                $container->singleton(IProject::class, $project);
            }
        );

        $this->assertContains(sprintf('Option file %s cache successed.', $cacheFile), $result);

        $this->assertSame(['foo' => 'bar', 'hello' => 'world'], (array) (include $cacheFile));

        unlink($cacheFile);
        rmdir(dirname($cacheFile));
    }

    public function testDirWriteable()
    {
        $dirname = __DIR__.'/dirWriteable';
        $cacheFile = $dirname.'/option_cache.php';

        // 设置目录只读
        // 7 = 4+2+1 分别代表可读可写可执行
        mkdir($dirname, 0444);

        $this->assertDirectoryExists($dirname);

        $result = $this->runCommand(
            new Cache(),
            [
                'command'     => 'option:cache',
            ],
            function ($container) use ($cacheFile) {
                // 静态属性会保持住，可能受到其它单元测试的影响
                Facade::remove('option');

                $container->singleton('option', function () {
                    return new OptionService();
                });

                $project = $this->createMock(IProject::class);

                $this->assertInstanceof(IProject::class, $project);

                $project->method('pathCacheOptionFile')->willReturn($cacheFile);
                $this->assertEquals($cacheFile, $project->pathCacheOptionFile());

                $container->singleton(IProject::class, $project);
            }
        );

        $this->assertContains('not', $result);

        rmdir($dirname);
    }
}

class OptionService
{
    public function all()
    {
        return [
            'foo'   => 'bar',
            'hello' => 'world',
        ];
    }
}
