<?php

declare(strict_types=1);

namespace Tests\Kernel\Console;

use Leevel\Di\IContainer;
use Leevel\Filesystem\Helper;
use Leevel\Kernel\App as Apps;
use Leevel\Kernel\Console\LinkStatic;
use Leevel\Kernel\IApp;
use Tests\Console\BaseCommand;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class LinkStaticTest extends TestCase
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
            new LinkStatic(),
            [
                'command' => 'link:static',
            ],
            function ($container): void {
                $this->initContainerService($container);
            }
        );
        $result = $this->normalizeContent($result);

        static::assertStringContainsString(
            $this->normalizeContent(sprintf('Linked `%s/assert/static` directory to `%s/assert_new/static` successed.', __DIR__, __DIR__)),
            $result
        );
        static::assertTrue(is_file(__DIR__.'/assert_new/static'));
    }

    protected function initContainerService(IContainer $container): void
    {
        $app = new AppForLinkStatic($container, '');
        $this->assertInstanceof(IApp::class, $app);
        $container->singleton(IApp::class, $app);
    }
}

class AppForLinkStatic extends Apps
{
    public function path(string $path = ''): string
    {
        return 'assets/static' === $path ?
                __DIR__.'/assert/static' :
                __DIR__.'/assert_new/static';
    }

    protected function registerBaseProvider(): void
    {
    }
}
