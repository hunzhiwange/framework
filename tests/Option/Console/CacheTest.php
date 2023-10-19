<?php

declare(strict_types=1);

namespace Tests\Option\Console;

use Leevel\Di\IContainer;
use Leevel\Filesystem\Helper;
use Leevel\Kernel\IApp;
use Leevel\Option\Console\Cache;
use Tests\Console\BaseCommand;
use Tests\TestCase;

final class CacheTest extends TestCase
{
    use BaseCommand;

    protected function setUp(): void
    {
        $dirs = [
            __DIR__.'/dirNotExists',
            __DIR__.'/assertRelative/relative',
        ];
        foreach ($dirs as $dir) {
            if (is_dir($dir)) {
                Helper::deleteDirectory($dir);
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

    public function testBaseUse(): void
    {
        $cacheFile = __DIR__.'/option_cache.php';

        $result = $this->runCommand(
            new Cache(),
            [
                'command' => 'option:cache',
            ],
            function ($container) use ($cacheFile): void {
                $this->initContainerService($container, $cacheFile);
            }
        );

        $result = $this->normalizeContent($result);

        static::assertStringContainsString(
            $this->normalizeContent('Start to cache option.'),
            $result
        );

        static::assertStringContainsString(
            $this->normalizeContent(sprintf('Option cache succeed at %s.', $cacheFile)),
            $result
        );

        $optionData = file_get_contents(__DIR__.'/assert/option.json');

        static::assertSame(
            trim($optionData),
            $this->varJson(
                (array) (include $cacheFile)
            )
        );

        unlink($cacheFile);
    }

    public function testDirNotExists(): void
    {
        $cacheFile = __DIR__.'/dirNotExists/option_cache.php';

        $result = $this->runCommand(
            new Cache(),
            [
                'command' => 'option:cache',
            ],
            function ($container) use ($cacheFile): void {
                $this->initContainerService($container, $cacheFile);
            }
        );

        $result = $this->normalizeContent($result);

        static::assertStringContainsString(
            $this->normalizeContent('Start to cache option.'),
            $result
        );

        static::assertStringContainsString(
            $this->normalizeContent(sprintf('Option cache succeed at %s.', $cacheFile)),
            $result
        );

        $optionData = file_get_contents(__DIR__.'/assert/option.json');

        static::assertSame(
            trim($optionData),
            $this->varJson(
                (array) (include $cacheFile)
            )
        );

        unlink($cacheFile);
        rmdir(\dirname($cacheFile));
    }

    public function testDirRelative(): void
    {
        $cacheFile = __DIR__.'/assertRelative/relative/k/option_cache.php';

        $result = $this->runCommand(
            new Cache(),
            [
                'command' => 'option:cache',
            ],
            function ($container) use ($cacheFile): void {
                $this->initContainerService($container, $cacheFile, 'assertRelative');
            }
        );

        $result = $this->normalizeContent($result);

        static::assertStringContainsString(
            $this->normalizeContent('Start to cache option.'),
            $result
        );

        static::assertStringContainsString(
            $this->normalizeContent(sprintf('Option cache succeed at %s.', $cacheFile)),
            $result
        );

        $optionData = (array) (include __DIR__.'/assertRelative/option.php');

        static::assertSame(
            $optionData,
            (array) (include $cacheFile)
        );

        unlink($cacheFile);
        rmdir(\dirname($cacheFile));
    }

    protected function initContainerService(IContainer $container, string $cacheFile, string $assertDir = 'assert'): void
    {
        $app = $this->createMock(IApp::class);
        $this->assertInstanceof(IApp::class, $app);

        $app->method('path')->willReturn(__DIR__.'/'.$assertDir);
        static::assertSame(__DIR__.'/'.$assertDir, $app->path());

        $app->method('envPath')->willReturn(__DIR__.'/'.$assertDir);
        static::assertSame(__DIR__.'/'.$assertDir, $app->envPath());

        $app->method('envFile')->willReturn('.env');
        static::assertSame('.env', $app->envFile());

        $app->method('optionCachedPath')->willReturn($cacheFile);
        static::assertSame($cacheFile, $app->optionCachedPath());

        $app->method('optionPath')->willReturn(__DIR__.'/'.$assertDir.'/option');
        static::assertSame(__DIR__.'/'.$assertDir.'/option', $app->optionPath());

        $container->singleton(IApp::class, $app);
    }
}
