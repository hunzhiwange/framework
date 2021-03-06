<?php

declare(strict_types=1);

namespace Tests\Option\Console;

use Leevel\Di\IContainer;
use Leevel\Kernel\IApp;
use Leevel\Option\Console\Clear;
use Tests\Console\BaseCommand;
use Tests\TestCase;

class ClearTest extends TestCase
{
    use BaseCommand;

    public function testBaseUse(): void
    {
        $cacheFile = __DIR__.'/option_clear.php';

        file_put_contents($cacheFile, 'foo');

        $result = $this->runCommand(
            new Clear(),
            [
                'command' => 'option:clear',
            ],
            function ($container) use ($cacheFile) {
                $this->initContainerService($container, $cacheFile);
            }
        );

        $result = $this->normalizeContent($result);

        $this->assertStringContainsString(
            $this->normalizeContent(sprintf('Option cache files %s clear successed.', $cacheFile)),
            $result
        );

        $this->assertStringNotContainsString(
            $this->normalizeContent(sprintf('Option cache files %s have been cleaned up.', $cacheFile)),
            $result
        );
    }

    public function testHaveCleanedUp(): void
    {
        $cacheFile = __DIR__.'/option_clear2.php';

        $result = $this->runCommand(
            new Clear(),
            [
                'command' => 'option:clear',
            ],
            function ($container) use ($cacheFile) {
                $this->initContainerService($container, $cacheFile);
            }
        );

        $result = $this->normalizeContent($result);

        $this->assertStringContainsString(
            $this->normalizeContent(sprintf('Option cache files %s clear successed.', $cacheFile)),
            $result
        );

        $this->assertStringContainsString(
            $this->normalizeContent(sprintf('Option cache files %s have been cleaned up.', $cacheFile)),
            $result
        );
    }

    protected function initContainerService(IContainer $container, string $cacheFile): void
    {
        $app = $this->createMock(IApp::class);
        $this->assertInstanceof(IApp::class, $app);

        $app->method('optionCachedPath')->willReturn($cacheFile);
        $this->assertEquals($cacheFile, $app->optionCachedPath());

        $container->singleton(IApp::class, $app);
    }
}
