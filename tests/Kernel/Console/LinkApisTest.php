<?php

declare(strict_types=1);

namespace Tests\Kernel\Console;

use Leevel\Di\IContainer;
use Leevel\Filesystem\Helper;
use Leevel\Kernel\App as Apps;
use Leevel\Kernel\Console\LinkApis;
use Leevel\Kernel\IApp;
use Tests\Console\BaseCommand;
use Tests\TestCase;

final class LinkApisTest extends TestCase
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
            new LinkApis(),
            [
                'command' => 'link:apis',
            ],
            function ($container): void {
                $this->initContainerService($container);
            }
        );

        $result = $this->normalizeContent($result);

        static::assertStringContainsString(
            $this->normalizeContent(sprintf('Linked `%s/assert/apis` directory to `%s/assert_new/apis` succeed.', __DIR__, __DIR__)),
            $result
        );
        static::assertTrue(is_file(__DIR__.'/assert_new/apis'));
    }

    protected function initContainerService(IContainer $container): void
    {
        $app = new AppForLinkApis($container, '');
        $this->assertInstanceof(IApp::class, $app);
        $container->singleton(IApp::class, $app);
    }
}

class AppForLinkApis extends Apps
{
    public function path(string $path = ''): string
    {
        if ('www/apis' === $path) {
            return __DIR__.'/assert_new/apis';
        }

        return __DIR__.'/assert/apis';
    }

    protected function registerBaseProvider(): void
    {
    }
}
