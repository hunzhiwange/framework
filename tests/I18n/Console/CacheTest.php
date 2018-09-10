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

namespace Tests\I18n\Console;

use Leevel\Bootstrap\Project as Projects;
use Leevel\Di\IContainer;
use Leevel\Filesystem\Fso;
use Leevel\I18n\Console\Cache;
use Leevel\Kernel\IProject;
use Leevel\Option\IOption;
use Leevel\Option\Option;
use Tests\Console\BaseCommand;
use Tests\TestCase;

/**
 * cache test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.07.18
 *
 * @version 1.0
 */
class CacheTest extends TestCase
{
    use BaseCommand;

    protected function setUp()
    {
        $dirs = [
            __DIR__.'/dirWriteable',
            __DIR__.'/parentDirWriteable',
        ];

        foreach ($dirs as $dir) {
            if (is_dir($dir)) {
                Fso::deleteDirectory($dir, true);
            }
        }
    }

    protected function tearDown()
    {
        $this->setUp();
    }

    public function testBaseUse()
    {
        $cacheFile = __DIR__.'/i18n_cache_[i18n].php';

        $cacheData = [
            'zh-CN' => [
            ],
            'zh-TW' => [
                '上一页'    => '上一頁',
                '下一页'    => '下一頁',
                '共 %d 条' => '共 %d 條',
                '前往'     => '前往',
                '页'      => '頁',
            ],
            'en-US' => [
                '上一页'    => 'Previous',
                '下一页'    => 'Next',
                '共 %d 条' => 'Total %d',
                '前往'     => 'Go to',
                '页'      => 'Page',
            ],
        ];

        $result = $this->runCommand(
            new Cache(),
            [
                'command' => 'i18n:cache',
            ],
            function ($container) use ($cacheFile, $cacheData) {
                $this->initContainerService($container, $cacheFile, $cacheData);
            }
        );

        $result = $this->normalizeContent($result);

        $this->assertContains(
            $this->normalizeContent('Start to cache i18n.'),
            $result
        );

        foreach (['zh-CN', 'zh-TW', 'en-US'] as $i18n) {
            $this->assertContains(
                $this->normalizeContent(
                    sprintf(
                        'I18n file %s cache successed.',
                        $cacheFileForI18n = str_replace('[i18n]', $i18n, $cacheFile)
                    )
                ),
                $result
            );

            $this->assertSame($cacheData[$i18n], (array) (include $cacheFileForI18n));

            unlink($cacheFileForI18n);
        }

        $this->assertContains(
            $this->normalizeContent('I18n files cache successed.'),
            $result
        );
    }

    public function testDirNotExists()
    {
        $cacheFile = __DIR__.'/dirNotExists/i18n_cache_[i18n].php';

        $cacheData = [
            'zh-CN' => [
            ],
            'zh-TW' => [
                '上一页'    => '上一頁',
                '下一页'    => '下一頁',
                '共 %d 条' => '共 %d 條',
                '前往'     => '前往',
                '页'      => '頁',
            ],
            'en-US' => [
                '上一页'    => 'Previous',
                '下一页'    => 'Next',
                '共 %d 条' => 'Total %d',
                '前往'     => 'Go to',
                '页'      => 'Page',
            ],
        ];

        $result = $this->runCommand(
            new Cache(),
            [
                'command' => 'i18n:cache',
            ],
            function ($container) use ($cacheFile, $cacheData) {
                $this->initContainerService($container, $cacheFile, $cacheData);
            }
        );

        $result = $this->normalizeContent($result);

        $this->assertContains(
            $this->normalizeContent('Start to cache i18n.'),
            $result
        );

        foreach (['zh-CN', 'zh-TW', 'en-US'] as $i18n) {
            $this->assertContains(
                $this->normalizeContent(
                    sprintf(
                        'I18n file %s cache successed.',
                        $cacheFileForI18n = str_replace('[i18n]', $i18n, $cacheFile)
                    )
                ),
                $result
            );

            $this->assertSame($cacheData[$i18n], (array) (include $cacheFileForI18n));

            unlink($cacheFileForI18n);
        }

        $this->assertContains(
            $this->normalizeContent('I18n files cache successed.'),
            $result
        );

        rmdir(dirname($cacheFile));
    }

    public function testDirWriteable()
    {
        $dirname = __DIR__.'/dirWriteable';
        $cacheFile = $dirname.'/i18n_cache_[i18n].php';

        $cacheData = [
            'zh-CN' => [
            ],
            'zh-TW' => [
                '上一页'    => '上一頁',
                '下一页'    => '下一頁',
                '共 %d 条' => '共 %d 條',
                '前往'     => '前往',
                '页'      => '頁',
            ],
            'en-US' => [
                '上一页'    => 'Previous',
                '下一页'    => 'Next',
                '共 %d 条' => 'Total %d',
                '前往'     => 'Go to',
                '页'      => 'Page',
            ],
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
                'command' => 'i18n:cache',
            ],
            function ($container) use ($cacheFile, $cacheData) {
                $this->initContainerService($container, $cacheFile, $cacheData);
            }
        );

        $result = $this->normalizeContent($result);

        $this->assertContains(
            $this->normalizeContent('Start to cache i18n.'),
            $result
        );

        $this->assertContains(
            $this->normalizeContent(sprintf('Dir %s is not writeable.', $dirname)),
            $result
        );

        rmdir($dirname);
    }

    public function testParentDirWriteable()
    {
        $dirname = __DIR__.'/parentDirWriteable/sub';
        $cacheFile = $dirname.'/i18n_cache_[i18n].php';

        $cacheData = [
            'zh-CN' => [
            ],
            'zh-TW' => [
                '上一页'    => '上一頁',
                '下一页'    => '下一頁',
                '共 %d 条' => '共 %d 條',
                '前往'     => '前往',
                '页'      => '頁',
            ],
            'en-US' => [
                '上一页'    => 'Previous',
                '下一页'    => 'Next',
                '共 %d 条' => 'Total %d',
                '前往'     => 'Go to',
                '页'      => 'Page',
            ],
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
                'command' => 'i18n:cache',
            ],
            function ($container) use ($cacheFile, $cacheData) {
                $this->initContainerService($container, $cacheFile, $cacheData);
            }
        );

        $result = $this->normalizeContent($result);

        $this->assertContains(
            $this->normalizeContent('Start to cache i18n.'),
            $result
        );

        $this->assertContains(
            $this->normalizeContent(sprintf('Unable to create the %s directory.', $dirname)),
            $result
        );

        rmdir(dirname($dirname));
    }

    protected function initContainerService(IContainer $container, string $cacheFile, array $cacheData)
    {
        // 注册 project
        $project = new Project();
        $this->assertInstanceof(IProject::class, $project);

        $project->setCacheFile($cacheFile);

        foreach (['zh-CN', 'zh-TW', 'en-US'] as $i18n) {
            $this->assertEquals(
                str_replace('[i18n]', $i18n, $cacheFile),
                $project->i18nCachedPath($i18n)
            );
        }

        $container->singleton(IProject::class, $project);

        // 注册 option
        $option = new Option([
            'app' => [
                '_composer' => [
                    'i18ns' => [],
                ],
            ],
            'i18n' => [
                'default' => 'en-US',
            ],
        ]);

        $project->singleton('option', function () use ($option) {
            return $option;
        });

        $container->singleton(IOption::class, $option);
    }
}

class Project extends Projects
{
    protected $cacheFile;

    public function setCacheFile(string $cacheFile)
    {
        $this->cacheFile = $cacheFile;
    }

    public function i18nCachedPath($i18n): string
    {
        return str_replace('[i18n]', $i18n, $this->cacheFile);
    }

    public function i18nPath($path = null)
    {
        return __DIR__.'/i18n';
    }

    protected function registerBaseProvider()
    {
    }
}
