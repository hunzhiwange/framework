<?php

declare(strict_types=1);

namespace Tests\Kernel\Console;

use Leevel\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Leevel\Di\IContainer;
use Leevel\Kernel\App as Apps;
use Leevel\Kernel\Console\Development;
use Leevel\Kernel\IApp;
use Tests\Console\BaseCommand;
use Tests\TestCase;

class DevelopmentTest extends TestCase
{
    use BaseCommand;

    public function testBaseUse(): void
    {
        $result = $this->runCommand(
            new Development(),
            [
                'command' => 'development',
            ],
            function ($container) {
                $this->initContainerService($container);
            },
            [
                new DemoI18nClear(),
                new DemoLogClear(),
                new DemoOptionClear(),
                new DemoRouterClear(),
                new DemoSessionClear(),
                new DemoViewClear(),
                new DemoAutoloadClear(),
            ]
        );

        $result = $this->normalizeContent($result);

        $this->assertStringContainsString(
            $this->normalizeContent('Start to clears caches.'),
            $result
        );
        $this->assertStringContainsString(
            $this->normalizeContent('Caches cleared successed.'),
            $result
        );
    }

    protected function initContainerService(IContainer $container): void
    {
        $app = new AppForDevelopment($container, '');
        $this->assertInstanceof(IApp::class, $app);
        $container->singleton(IApp::class, $app);
    }
}

class AppForDevelopment extends Apps
{
    protected function registerBaseProvider(): void
    {
    }
}

class DemoI18nClear extends Command
{
    protected string $name = 'i18n:clear';

    protected string $description = 'This is a demo command';

    public function handle(): int
    {
        return 0;
    }
}

class DemoLogClear extends Command
{
    protected string $name = 'log:clear';

    protected string $description = 'This is a demo command';

    public function handle(): int
    {
        return 0;
    }
}

class DemoOptionClear extends Command
{
    protected string $name = 'option:clear';

    protected string $description = 'This is a demo command';

    public function handle(): int
    {
        return 0;
    }
}

class DemoRouterClear extends Command
{
    protected string $name = 'router:clear';

    protected string $description = 'This is a demo command';

    public function handle(): int
    {
        return 0;
    }
}

class DemoSessionClear extends Command
{
    protected string $name = 'session:clear';

    protected string $description = 'This is a demo command';

    public function handle(): int
    {
        return 0;
    }
}

class DemoViewClear extends Command
{
    protected string $name = 'view:clear';

    protected string $description = 'This is a demo command';

    public function handle(): int
    {
        return 0;
    }
}

class DemoAutoloadClear extends Command
{
    protected string $name = 'autoload';

    protected string $description = 'This is a demo command';

    public function handle(): int
    {
        return 0;
    }

    protected function getOptions(): array
    {
        return [
            [
                'composer',
                null,
                InputOption::VALUE_OPTIONAL,
                'Where is composer.',
                'composer',
            ],
            [
                'dev',
                '-d',
                InputOption::VALUE_NONE,
                'Without `--no-dev` option for `composer dump-autoload --optimize`.',
            ],
        ];
    }
}
