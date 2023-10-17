<?php

declare(strict_types=1);

namespace Tests\I18n\Console;

use Leevel\Di\IContainer;
use Leevel\I18n\Console\Cache;
use Leevel\Kernel\App as Apps;
use Leevel\Kernel\IApp;
use Leevel\Option\IOption;
use Leevel\Option\Option;
use Tests\Console\BaseCommand;
use Tests\TestCase;

/**
 * @internal
 */
final class CacheTest extends TestCase
{
    use BaseCommand;

    public function testBaseUse(): void
    {
        $cacheFile = __DIR__.'/i18n_cache_[i18n].php';

        $cacheData = [
            'zh-CN' => [
            ],
            'zh-TW' => [
                '上一页' => '上一頁',
                '下一页' => '下一頁',
                '共 %d 条' => '共 %d 條',
                '前往' => '前往',
                '页' => '頁',
            ],
            'en-US' => [
                '上一页' => 'Previous',
                '下一页' => 'Next',
                '共 %d 条' => 'Total %d',
                '前往' => 'Go to',
                '页' => 'Page',
            ],
        ];

        $result = $this->runCommand(
            new Cache(),
            [
                'command' => 'i18n:cache',
            ],
            function ($container) use ($cacheFile): void {
                $this->initContainerService($container, $cacheFile);
            }
        );

        $result = $this->normalizeContent($result);

        static::assertStringContainsString(
            $this->normalizeContent('Start to cache i18n.'),
            $result
        );

        foreach (['zh-CN', 'zh-TW', 'en-US'] as $i18n) {
            static::assertStringContainsString(
                $this->normalizeContent(
                    sprintf(
                        'I18n cache succeed at %s.',
                        $cacheFileForI18n = str_replace('[i18n]', $i18n, $cacheFile)
                    )
                ),
                $result
            );

            static::assertSame($cacheData[$i18n], (array) (include $cacheFileForI18n));

            unlink($cacheFileForI18n);
        }

        static::assertStringContainsString(
            $this->normalizeContent('I18n cache succeed.'),
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
                '上一页' => '上一頁',
                '下一页' => '下一頁',
                '共 %d 条' => '共 %d 條',
                '前往' => '前往',
                '页' => '頁',
            ],
            'en-US' => [
                '上一页' => 'Previous',
                '下一页' => 'Next',
                '共 %d 条' => 'Total %d',
                '前往' => 'Go to',
                '页' => 'Page',
            ],
        ];

        $result = $this->runCommand(
            new Cache(),
            [
                'command' => 'i18n:cache',
            ],
            function ($container) use ($cacheFile): void {
                $this->initContainerService($container, $cacheFile);
            }
        );

        $result = $this->normalizeContent($result);

        static::assertStringContainsString(
            $this->normalizeContent('Start to cache i18n.'),
            $result
        );

        foreach (['zh-CN', 'zh-TW', 'en-US'] as $i18n) {
            static::assertStringContainsString(
                $this->normalizeContent(
                    sprintf(
                        'I18n cache succeed at %s.',
                        $cacheFileForI18n = str_replace('[i18n]', $i18n, $cacheFile)
                    )
                ),
                $result
            );

            static::assertSame($cacheData[$i18n], (array) (include $cacheFileForI18n));

            unlink($cacheFileForI18n);
        }

        static::assertStringContainsString(
            $this->normalizeContent('I18n cache succeed.'),
            $result
        );

        rmdir(\dirname($cacheFile));
    }

    protected function initContainerService(IContainer $container, string $cacheFile): void
    {
        $app = new App($container, '');
        $this->assertInstanceof(IApp::class, $app);

        $app->setCacheFile($cacheFile);

        foreach (['zh-CN', 'zh-TW', 'en-US'] as $i18n) {
            static::assertSame(
                str_replace('[i18n]', $i18n, $cacheFile),
                $app->i18nCachedPath($i18n)
            );
        }

        $container->singleton(IApp::class, $app);

        // 注册 option
        $option = new Option([
            'app' => [
                ':composer' => [
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

    public function setCacheFile(string $cacheFile): void
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
