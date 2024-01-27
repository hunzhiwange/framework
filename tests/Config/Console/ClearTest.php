<?php

declare(strict_types=1);

namespace Tests\Config\Console;

use Leevel\Config\Console\Clear;
use Leevel\Di\IContainer;
use Leevel\Kernel\IApp;
use Tests\Console\BaseCommand;
use Tests\TestCase;

final class ClearTest extends TestCase
{
    use BaseCommand;

    public function testBaseUse(): void
    {
        $cacheFile = __DIR__.'/config_clear.php';

        file_put_contents($cacheFile, 'foo');

        $result = $this->runCommand(
            new Clear(),
            [
                'command' => 'config:clear',
            ],
            function ($container) use ($cacheFile): void {
                $this->initContainerService($container, $cacheFile);
            }
        );

        $result = $this->normalizeContent($result);

        static::assertStringContainsString(
            $this->normalizeContent(sprintf('Config cache files %s clear succeed.', $cacheFile)),
            $result
        );

        static::assertStringNotContainsString(
            $this->normalizeContent(sprintf('Config cache files %s have been cleaned up.', $cacheFile)),
            $result
        );
    }

    public function testHaveCleanedUp(): void
    {
        $cacheFile = __DIR__.'/config_clear2.php';

        $result = $this->runCommand(
            new Clear(),
            [
                'command' => 'config:clear',
            ],
            function ($container) use ($cacheFile): void {
                $this->initContainerService($container, $cacheFile);
            }
        );

        $result = $this->normalizeContent($result);

        static::assertStringContainsString(
            $this->normalizeContent(sprintf('Config cache files %s clear succeed.', $cacheFile)),
            $result
        );

        static::assertStringContainsString(
            $this->normalizeContent(sprintf('Config cache files %s have been cleaned up.', $cacheFile)),
            $result
        );
    }

    protected function initContainerService(IContainer $container, string $cacheFile): void
    {
        $app = $this->createMock(IApp::class);
        $this->assertInstanceof(IApp::class, $app);

        $app->method('configCachedPath')->willReturn($cacheFile);
        static::assertSame($cacheFile, $app->configCachedPath());

        $container->singleton(IApp::class, $app);
    }
}
