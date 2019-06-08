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

namespace Tests\I18n\Console;

use Leevel\Di\IContainer;
use Leevel\Filesystem\Fso;
use Leevel\I18n\Console\Cache;
use Leevel\Kernel\App as Apps;
use Leevel\Kernel\IApp;
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

    protected function setUp(): void
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

    protected function tearDown(): void
    {
        $this->setUp();
    }

    public function testBaseUse(): void
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
            function ($container) use ($cacheFile) {
                $this->initContainerService($container, $cacheFile);
            }
        );

        $result = $this->normalizeContent($result);

        $this->assertStringContainsString(
            $this->normalizeContent('Start to cache i18n.'),
            $result
        );

        foreach (['zh-CN', 'zh-TW', 'en-US'] as $i18n) {
            $this->assertStringContainsString(
                $this->normalizeContent(
                    sprintf(
                        'I18n cache file %s cache successed.',
                        $cacheFileForI18n = str_replace('[i18n]', $i18n, $cacheFile)
                    )
                ),
                $result
            );

            $this->assertSame($cacheData[$i18n], (array) (include $cacheFileForI18n));

            unlink($cacheFileForI18n);
        }

        $this->assertStringContainsString(
            $this->normalizeContent('I18n cache files cache successed.'),
            $result
        );
    }

    public function testDirNotExists(): void
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
            function ($container) use ($cacheFile) {
                $this->initContainerService($container, $cacheFile);
            }
        );

        $result = $this->normalizeContent($result);

        $this->assertStringContainsString(
            $this->normalizeContent('Start to cache i18n.'),
            $result
        );

        foreach (['zh-CN', 'zh-TW', 'en-US'] as $i18n) {
            $this->assertStringContainsString(
                $this->normalizeContent(
                    sprintf(
                        'I18n cache file %s cache successed.',
                        $cacheFileForI18n = str_replace('[i18n]', $i18n, $cacheFile)
                    )
                ),
                $result
            );

            $this->assertSame($cacheData[$i18n], (array) (include $cacheFileForI18n));

            unlink($cacheFileForI18n);
        }

        $this->assertStringContainsString(
            $this->normalizeContent('I18n cache files cache successed.'),
            $result
        );

        rmdir(dirname($cacheFile));
    }

    public function testDirWriteable(): void
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
            function ($container) use ($cacheFile) {
                $this->initContainerService($container, $cacheFile);
            }
        );

        $result = $this->normalizeContent($result);

        $this->assertStringContainsString(
            $this->normalizeContent('Start to cache i18n.'),
            $result
        );

        $this->assertStringContainsString(
            $this->normalizeContent(sprintf('Dir `%s` is not writeable.', $dirname)),
            $result
        );

        rmdir($dirname);
    }

    public function testParentDirWriteable(): void
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
            function ($container) use ($cacheFile) {
                $this->initContainerService($container, $cacheFile);
            }
        );

        $result = $this->normalizeContent($result);

        $this->assertStringContainsString(
            $this->normalizeContent('Start to cache i18n.'),
            $result
        );

        $this->assertStringContainsString(
            $this->normalizeContent(sprintf('Dir `%s` is not writeable.', dirname($dirname))),
            $result
        );

        rmdir(dirname($dirname));
    }

    protected function initContainerService(IContainer $container, string $cacheFile)
    {
        // 注册 app
        $app = new App($container, '');
        $this->assertInstanceof(IApp::class, $app);

        $app->setCacheFile($cacheFile);

        foreach (['zh-CN', 'zh-TW', 'en-US'] as $i18n) {
            $this->assertEquals(
                str_replace('[i18n]', $i18n, $cacheFile),
                $app->i18nCachedPath($i18n)
            );
        }

        $container->singleton(IApp::class, $app);

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

        $container->singleton('option', function () use ($option) {
            return $option;
        });

        $container->singleton(IOption::class, $option);
    }
}

class App extends Apps
{
    protected $cacheFile;

    public function setCacheFile(string $cacheFile)
    {
        $this->cacheFile = $cacheFile;
    }

    public function i18nCachedPath(string $i18n): string
    {
        return str_replace('[i18n]', $i18n, $this->cacheFile);
    }

    public function i18nPath(?string $path = null): string
    {
        return __DIR__.'/i18n';
    }

    protected function registerBaseProvider(): void
    {
    }
}
