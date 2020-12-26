<?php

declare(strict_types=1);

namespace Tests\Log\Console;

use Leevel\Di\IContainer;
use Leevel\Filesystem\Helper;
use Leevel\Kernel\IApp;
use Leevel\Log\Console\Clear;
use Tests\Console\BaseCommand;
use Tests\TestCase;

class ClearTest extends TestCase
{
    use BaseCommand;

    public function testBaseUse(): void
    {
        $cacheDir = __DIR__.'/log_clear';
        Helper::createFile($cacheDir.'/hello.txt', 'foo');
        $this->assertDirectoryExists($cacheDir);

        $result = $this->runCommand(
            new Clear(),
            [
                'command' => 'log:clear',
            ],
            function ($container) use ($cacheDir) {
                $this->initContainerService($container, $cacheDir);
            }
        );

        $this->assertDirectoryDoesNotExist($cacheDir);

        $result = $this->normalizeContent($result);
        $this->assertStringContainsString(
            $this->normalizeContent('Start to clear cache log.'),
            $result
        );
        $this->assertStringContainsString(
            $this->normalizeContent(sprintf('Log cache files in path %s clear successed.', $cacheDir)),
            $result
        );
    }

    protected function initContainerService(IContainer $container, string $cacheDir): void
    {
        $app = $this->createMock(IApp::class);
        $this->assertInstanceof(IApp::class, $app);

        $app->method('storagePath')->willReturn($cacheDir);
        $this->assertEquals($cacheDir, $app->storagePath('log'));

        $container->singleton(IApp::class, $app);
    }
}
