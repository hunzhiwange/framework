<?php

declare(strict_types=1);

namespace Tests\Config\Console;

use Leevel\Config\Console\Cache;
use Leevel\Di\IContainer;
use Leevel\Filesystem\Helper\DeleteDirectory;
use Leevel\Kernel\IApp;
use Tests\Console\BaseCommand;
use Tests\TestCase;

final class CacheTest extends TestCase
{
    use BaseCommand;

    #[\Override]
    protected function setUp(): void
    {
        $dirs = [
            __DIR__.'/dirNotExists',
            __DIR__.'/assertRelative/relative',
        ];
        foreach ($dirs as $dir) {
            if (is_dir($dir)) {
                DeleteDirectory::handle($dir);
            }
        }

        $file = __DIR__.'/config_cache.php';
        if (is_file($file)) {
            unlink($file);
        }
    }

    #[\Override]
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

        self::assertStringContainsString(
            $this->normalizeContent('Start to cache config.'),
            $result
        );

        self::assertStringContainsString(
            $this->normalizeContent(\sprintf('Config cache succeed at %s.', $cacheFile)),
            $result
        );

        $configData = file_get_contents(__DIR__.'/assert/config.json');

        self::assertSame(
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

        self::assertStringContainsString(
            $this->normalizeContent('Start to cache config.'),
            $result
        );

        self::assertStringContainsString(
            $this->normalizeContent(\sprintf('Config cache succeed at %s.', $cacheFile)),
            $result
        );

        $configData = file_get_contents(__DIR__.'/assert/config.json');

        self::assertSame(
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

        self::assertStringContainsString(
            $this->normalizeContent('Start to cache config.'),
            $result
        );

        self::assertStringContainsString(
            $this->normalizeContent(\sprintf('Config cache succeed at %s.', $cacheFile)),
            $result
        );

        $configData = (array) (include __DIR__.'/assertRelative/config.php');

        self::assertSame(
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
        self::assertSame(__DIR__.'/'.$assertDir, $app->path());

        $app->method('envPath')->willReturn(__DIR__.'/'.$assertDir);
        self::assertSame(__DIR__.'/'.$assertDir, $app->envPath());

        $app->method('envFile')->willReturn('.env');
        self::assertSame('.env', $app->envFile());

        $app->method('configCachedPath')->willReturn($cacheFile);
        self::assertSame($cacheFile, $app->configCachedPath());

        $app->method('configPath')->willReturn(__DIR__.'/'.$assertDir.'/config');
        self::assertSame(__DIR__.'/'.$assertDir.'/config', $app->configPath());

        $container->singleton(IApp::class, $app);
    }
}
