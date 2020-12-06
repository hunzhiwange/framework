<?php

declare(strict_types=1);

namespace Tests\Kernel\Console;

use Leevel\Di\IContainer;
use Leevel\Filesystem\Helper;
use Leevel\Kernel\App as Apps;
use Leevel\Kernel\Console\LinkPublic;
use Leevel\Kernel\IApp;
use Tests\Console\BaseCommand;
use Tests\TestCase;

class LinkPublicTest extends TestCase
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
            new LinkPublic(),
            [
                'command' => 'link:public',
            ],
            function ($container) {
                $this->initContainerService($container);
            }
        );

        $result = $this->normalizeContent($result);

        $this->assertStringContainsString(
            $this->normalizeContent(sprintf('Linked `%s/assert/public` directory to `%s/assert_new/public` successed.', __DIR__, __DIR__)),
            $result
        );
        $this->assertTrue(is_file(__DIR__.'/assert_new/public'));
    }

    public function testTargetFileAlreadyExists(): void
    {
        $targetFile = __DIR__.'/assert_new/public';
        Helper::createFile($targetFile, 'public');
        $this->assertTrue(is_file($targetFile));

        $result = $this->runCommand(
            new LinkPublic(),
            [
                'command' => 'link:public',
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
        $app = new AppForLinkPublic($container, '');
        $this->assertInstanceof(IApp::class, $app);
        $container->singleton(IApp::class, $app);
    }
}

class AppForLinkPublic extends Apps
{
    public function path(string $path = ''): string
    {
        return __DIR__.'/assert_new/public';
    }

    public function publicPath(string $path = ''): string
    {
        return __DIR__.'/assert/public';
    }

    protected function registerBaseProvider(): void
    {
    }
}
