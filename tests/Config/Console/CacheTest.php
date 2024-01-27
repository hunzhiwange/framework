<?php

declare(strict_types=1);

namespace Tests\Config\Console;

use Leevel\Config\Console\Cache;
use Leevel\Di\IContainer;
use Leevel\Filesystem\Helper;
use Leevel\Kernel\IApp;
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

        $file = __DIR__.'/config_cache.php';
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
        $cacheFile = __DIR__.'/config_cache.php';

        $result = $this->runCommand(
            new Cache(),
            [
                'command' => 'config:cache',
            ],
            function ($container) use ($cacheFile): void {
                $this->initContainerService($container, $cacheFile);
            }
        );

        $result = $this->normalizeContent($result);

        static::assertStringContainsString(
            $this->normalizeContent('Start to cache config.'),
            $result
        );

        static::assertStringContainsString(
            $this->normalizeContent(sprintf('Config cache succeed at %s.', $cacheFile)),
            $result
        );

        $configData = file_get_contents(__DIR__.'/assert/config.json');

        static::assertSame(
            trim($configData),
            $this->varJson(
                (array) (include $cacheFile)
            )
        );

        unlink($cacheFile);
    }

    public function testDirNotExists(): void
    {
        $cacheFile = __DIR__.'/dirNotExists/config_cache.php';

        $result = $this->runCommand(
            new Cache(),
            [
                'command' => 'config:cache',
            ],
            function ($container) use ($cacheFile): void {
                $this->initContainerService($container, $cacheFile);
            }
        );

        $result = $this->normalizeContent($result);

        static::assertStringContainsString(
            $this->normalizeContent('Start to cache config.'),
            $result
        );

        static::assertStringContainsString(
            $this->normalizeContent(sprintf('Config cache succeed at %s.', $cacheFile)),
            $result
        );

        $configData = file_get_contents(__DIR__.'/assert/config.json');

        static::assertSame(
            trim($configData),
            $this->varJson(
                (array) (include $cacheFile)
            )
        );

        unlink($cacheFile);
        rmdir(\dirname($cacheFile));
    }

    public function testDirRelative(): void
    {
        $cacheFile = __DIR__.'/assertRelative/relative/k/config_cache.php';

        $result = $this->runCommand(
            new Cache(),
            [
                'command' => 'config:cache',
            ],
            function ($container) use ($cacheFile): void {
                $this->initContainerService($container, $cacheFile, 'assertRelative');
            }
        );

        $result = $this->normalizeContent($result);

        static::assertStringContainsString(
            $this->normalizeContent('Start to cache config.'),
            $result
        );

        static::assertStringContainsString(
            $this->normalizeContent(sprintf('Config cache succeed at %s.', $cacheFile)),
            $result
        );

        $configData = (array) (include __DIR__.'/assertRelative/config.php');

        static::assertSame(
            $configData,
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

        $app->method('configCachedPath')->willReturn($cacheFile);
        static::assertSame($cacheFile, $app->configCachedPath());

        $app->method('configPath')->willReturn(__DIR__.'/'.$assertDir.'/config');
        static::assertSame(__DIR__.'/'.$assertDir.'/config', $app->configPath());

        $container->singleton(IApp::class, $app);
    }
}
