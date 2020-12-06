<?php

declare(strict_types=1);

namespace Tests\Kernel\Console;

use Leevel\Di\IContainer;
use Leevel\Filesystem\Helper;
use Leevel\Kernel\App as Apps;
use Leevel\Kernel\Console\LinkStorage;
use Leevel\Kernel\IApp;
use Tests\Console\BaseCommand;
use Tests\TestCase;

class LinkStorageTest extends TestCase
{
    use BaseCommand;

    protected function setUp(): void
    {
        $this->tearDown();
    }

    protected function tearDown(): void
    {
        $dirs = [
            __DIR__.'/assert_new',
        ];
        foreach ($dirs as $dir) {
            Helper::deleteDirectory($dir);
        }
    }

    public function testBaseUse(): void
    {
        $result = $this->runCommand(
            new LinkStorage(),
            [
                'command' => 'link:storage',
            ],
            function ($container) {
                $this->initContainerService($container);
            }
        );

        $result = $this->normalizeContent($result);

        $this->assertStringContainsString(
            $this->normalizeContent(sprintf('Linked `%s/assert/storage` directory to `%s/assert_new/storage` successed.', __DIR__, __DIR__)),
            $result
        );
        $this->assertTrue(is_file(__DIR__.'/assert_new/storage'));
    }

    public function testTargetFileAlreadyExists(): void
    {
        $targetFile = __DIR__.'/assert_new/storage';
        Helper::createFile($targetFile, 'storage');
        $this->assertTrue(is_file($targetFile));

        $result = $this->runCommand(
            new LinkStorage(),
            [
                'command' => 'link:storage',
            ],
            function ($container) {
                $this->initContainerService($container);
            }
        );

        $result = $this->normalizeContent($result);

        $this->assertStringContainsString(
            $this->normalizeContent(sprintf('The `%s` directory already exists.', $targetFile)),
            $result
        );
        $this->assertTrue(is_file($targetFile));
    }

    protected function initContainerService(IContainer $container): void
    {
        $app = new AppForLinkStorage($container, '');
        $this->assertInstanceof(IApp::class, $app);
        $container->singleton(IApp::class, $app);
    }
}

class AppForLinkStorage extends Apps
{
    public function path(string $path = ''): string
    {
        return __DIR__.'/assert_new/storage';
    }

    public function storagePath(string $path = ''): string
    {
        return __DIR__.'/assert/storage';
    }

    protected function registerBaseProvider(): void
    {
    }
}
