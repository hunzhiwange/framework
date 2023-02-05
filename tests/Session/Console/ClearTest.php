<?php

declare(strict_types=1);

namespace Tests\Session\Console;

use Leevel\Di\IContainer;
use Leevel\Filesystem\Helper;
use Leevel\Kernel\IApp;
use Leevel\Session\Console\Clear;
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
        $cacheDir = __DIR__.'/session_clear';
        Helper::createFile($cacheDir.'/hello.txt', 'foo');
        static::assertDirectoryExists($cacheDir);

        $result = $this->runCommand(
            new Clear(),
            [
                'command' => 'session:clear',
            ],
            function ($container) use ($cacheDir): void {
                $this->initContainerService($container, $cacheDir);
            }
        );

        static::assertDirectoryDoesNotExist($cacheDir);
        $result = $this->normalizeContent($result);
        static::assertStringContainsString(
            $this->normalizeContent('Start to clear cache session.'),
            $result
        );
        static::assertStringContainsString(
            $this->normalizeContent(sprintf('Session cache files in path %s clear successed.', $cacheDir)),
            $result
        );
    }

    protected function initContainerService(IContainer $container, string $cacheDir): void
    {
        $app = $this->createMock(IApp::class);
        $this->assertInstanceof(IApp::class, $app);

        $app->method('storagePath')->willReturn($cacheDir);
        static::assertSame($cacheDir, $app->storagePath('session'));

        $container->singleton(IApp::class, $app);
    }
}
