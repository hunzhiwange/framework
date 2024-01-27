<?php

declare(strict_types=1);

namespace Tests\Kernel\Console;

use Leevel\Console\Command;
use Leevel\Di\IContainer;
use Leevel\Kernel\App as Apps;
use Leevel\Kernel\Console\Links;
use Leevel\Kernel\IApp;
use Tests\Console\BaseCommand;
use Tests\TestCase;

final class LinksTest extends TestCase
{
    use BaseCommand;

    public function testBaseUse(): void
    {
        $result = $this->runCommand(
            new Links(),
            [
                'command' => 'links',
            ],
            function ($container): void {
                $this->initContainerService($container);
            },
            [
                new DemoLinkApis(),
                new DemoLinkStatic(),
                new DemoLinkAttachments(),
                new DemoLinkDebugbar(),
            ]
        );

        $result = $this->normalizeContent($result);
        static::assertStringContainsString(
            $this->normalizeContent('Start to create symbolic links.'),
            $result
        );
        static::assertStringContainsString(
            $this->normalizeContent('Links created succeed.'),
            $result
        );
    }

    protected function initContainerService(IContainer $container): void
    {
        $app = new AppForLinks($container, '');
        $this->assertInstanceof(IApp::class, $app);
        $container->singleton(IApp::class, $app);
    }
}

class AppForLinks extends Apps
{
    protected function registerBaseProvider(): void
    {
    }
}

class DemoLinkApis extends Command
{
    protected string $name = 'link:apis';

    protected string $description = 'This is a demo command';

    public function handle(): int
    {
        return self::SUCCESS;
    }
}

class DemoLinkStatic extends Command
{
    protected string $name = 'link:static';

    protected string $description = 'This is a demo command';

    public function handle(): int
    {
        return self::SUCCESS;
    }
}

class DemoLinkAttachments extends Command
{
    protected string $name = 'link:attachments';

    protected string $description = 'This is a demo command';

    public function handle(): int
    {
        return self::SUCCESS;
    }
}

class DemoLinkDebugbar extends Command
{
    protected string $name = 'link:debugbar';

    protected string $description = 'This is a demo command';

    public function handle(): int
    {
        return self::SUCCESS;
    }
}
