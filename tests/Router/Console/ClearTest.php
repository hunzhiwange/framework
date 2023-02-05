<?php

declare(strict_types=1);

namespace Tests\Router\Console;

use Leevel\Di\IContainer;
use Leevel\Kernel\IApp;
use Leevel\Router\Console\Clear;
use Tests\Console\BaseCommand;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class ClearTest extends TestCase
{
    use BaseCommand;

    public function testBaseUse(): void
    {
        $cacheFile = __DIR__.'/router_clear.php';

        file_put_contents($cacheFile, 'foo');

        $result = $this->runCommand(
            new Clear(),
            [
                'command' => 'router:clear',
            ],
            function ($container) use ($cacheFile): void {
                $this->initContainerService($container, $cacheFile);
            }
        );

        $result = $this->normalizeContent($result);

        static::assertStringContainsString(
            $this->normalizeContent(sprintf('Router cache files %s clear successed.', $cacheFile)),
            $result
        );

        static::assertStringNotContainsString(
            $this->normalizeContent(sprintf('Router cache file %s have been cleaned up.', $cacheFile)),
            $result
        );
    }

    public function testHaveCleanedUp(): void
    {
        $cacheFile = __DIR__.'/router_clear2.php';

        $result = $this->runCommand(
            new Clear(),
            [
                'command' => 'router:clear',
            ],
            function ($container) use ($cacheFile): void {
                $this->initContainerService($container, $cacheFile);
            }
        );

        $result = $this->normalizeContent($result);

        static::assertStringContainsString(
            $this->normalizeContent(sprintf('Router cache files %s clear successed.', $cacheFile)),
            $result
        );

        static::assertStringContainsString(
            $this->normalizeContent(sprintf('Router cache file %s have been cleaned up.', $cacheFile)),
            $result
        );
    }

    protected function initContainerService(IContainer $container, string $cacheFile): void
    {
        $app = $this->createMock(IApp::class);
        $this->assertInstanceof(IApp::class, $app);

        $app->method('routerCachedPath')->willReturn($cacheFile);
        static::assertSame($cacheFile, $app->routerCachedPath());

        $container->singleton(IApp::class, $app);
    }
}
