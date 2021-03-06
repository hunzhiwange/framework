<?php

declare(strict_types=1);

namespace Tests\Kernel\Console;

use Leevel\Di\IContainer;
use Leevel\Kernel\App as Apps;
use Leevel\Kernel\Console\Autoload;
use Leevel\Kernel\IApp;
use Tests\Console\BaseCommand;
use Tests\TestCase;

class AutoloadTest extends TestCase
{
    use BaseCommand;

    public function testBaseUse(): void
    {
        $result = $this->runCommand(
            new Autoload(),
            [
                'command' => 'autoload',
            ],
            function ($container) {
                $this->initContainerService($container);
            }
        );

        $result = $this->normalizeContent($result);

        $this->assertStringContainsString(
            $this->normalizeContent('Start to cache autoload.'),
            $result
        );
        $this->assertStringContainsString(
            $this->normalizeContent('\'composer\' dump-autoload --optimize --no-dev'),
            $result
        );
        $this->assertStringContainsString(
            $this->normalizeContent('Autoload cache successed.'),
            $result
        );
    }

    public function testWithDev(): void
    {
        $result = $this->runCommand(
            new Autoload(),
            [
                'command' => 'autoload',
                '--dev'   => true,
            ],
            function ($container) {
                $this->initContainerService($container);
            }
        );

        $result = $this->normalizeContent($result);

        $this->assertStringContainsString(
            $this->normalizeContent('Start to cache autoload.'),
            $result
        );
        $this->assertStringContainsString(
            $this->normalizeContent('\'composer\' dump-autoload --optimize'),
            $result
        );
        $this->assertStringContainsString(
            $this->normalizeContent('Autoload cache successed.'),
            $result
        );
    }

    protected function initContainerService(IContainer $container): void
    {
        $app = new AppForAutoload($container, '');
        $this->assertInstanceof(IApp::class, $app);
        $container->singleton(IApp::class, $app);
    }
}

class AppForAutoload extends Apps
{
    protected function registerBaseProvider(): void
    {
    }
}
