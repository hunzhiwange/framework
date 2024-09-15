<?php

declare(strict_types=1);

namespace Tests\View\Console;

use Leevel\Di\IContainer;
use Leevel\Filesystem\Helper\CreateFile;
use Leevel\Kernel\IApp;
use Leevel\View\Console\Clear;
use Tests\Console\BaseCommand;
use Tests\TestCase;

final class ClearTest extends TestCase
{
    use BaseCommand;

    public function testBaseUse(): void
    {
        $cacheDir = __DIR__.'/view_clear';
        CreateFile::handle($cacheDir.'/hello.txt', 'foo');
        self::assertDirectoryExists($cacheDir);

        $result = $this->runCommand(
            new Clear(),
            [
                'command' => 'view:clear',
            ],
            function ($container) use ($cacheDir): void {
                $this->initContainerService($container, $cacheDir);
            }
        );

        self::assertDirectoryDoesNotExist($cacheDir);
        $result = $this->normalizeContent($result);
        self::assertStringContainsString(
            $this->normalizeContent('Start to clear cache view.'),
            $result
        );
        self::assertStringContainsString(
            $this->normalizeContent(sprintf('View cache files in path %s clear succeed.', $cacheDir)),
            $result
        );
    }

    protected function initContainerService(IContainer $container, string $cacheDir): void
    {
        $app = $this->createMock(IApp::class);
        $this->assertInstanceof(IApp::class, $app);

        $app->method('storagePath')->willReturn($cacheDir);
        self::assertSame($cacheDir, $app->storagePath('theme'));

        $container->singleton(IApp::class, $app);
    }
}
