<?php

declare(strict_types=1);

namespace Tests\Debug\Console;

use Leevel\Debug\Console\LinkDebugBar;
use Leevel\Di\IContainer;
use Leevel\Filesystem\Helper;
use Leevel\Kernel\App as Apps;
use Leevel\Kernel\IApp;
use Tests\Console\BaseCommand;
use Tests\TestCase;

/**
 * @internal
 */
final class LinkDebugBarTest extends TestCase
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
            new LinkDebugBar(),
            [
                'command' => 'link:debugbar',
            ],
            function ($container): void {
                $this->initContainerService($container);
            }
        );

        $result = $this->normalizeContent($result);

        static::assertStringContainsString(
            $this->normalizeContent(sprintf('Linked `%s/assert/debugbar` directory to `%s/assert_new/debugbar1` successed.', __DIR__, __DIR__)),
            $result
        );
        static::assertStringContainsString(
            $this->normalizeContent(sprintf('Linked `%s/assert/debugbar` directory to `%s/assert_new/debugbar2` successed.', __DIR__, __DIR__)),
            $result
        );
        static::assertTrue(is_file(__DIR__.'/assert_new/debugbar1'));
        static::assertTrue(is_file(__DIR__.'/assert_new/debugbar2'));
    }

    protected function initContainerService(IContainer $container): void
    {
        $app = new AppForLinkDebugBar($container, '');
        $this->assertInstanceof(IApp::class, $app);
        $container->singleton(IApp::class, $app);
    }
}

class AppForLinkDebugBar extends Apps
{
    public function path(string $path = ''): string
    {
        if ('www/debugbar' === $path) {
            return __DIR__.'/assert_new/debugbar1';
        }

        if ('debugbar' === $path) {
            return __DIR__.'/assert_new/debugbar2';
        }

        return __DIR__.'/assert/debugbar';
    }

    protected function registerBaseProvider(): void
    {
    }
}
