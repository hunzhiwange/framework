<?php

declare(strict_types=1);

namespace Tests\Kernel\Console;

use Leevel\Console\Command;
use Leevel\Di\IContainer;
use Leevel\Kernel\App as Apps;
use Leevel\Kernel\Console\Production;
use Leevel\Kernel\IApp;
use Symfony\Component\Console\Input\InputOption;
use Tests\Console\BaseCommand;
use Tests\TestCase;

final class ProductionTest extends TestCase
{
    use BaseCommand;

    public function testBaseUse(): void
    {
        $result = $this->runCommand(
            new Production(),
            [
                'command' => 'production',
            ],
            function ($container): void {
                $this->initContainerService($container);
            },
            [
                new DemoRouterCache(),
                new DemoOptionCache(),
                new DemoI18nCache(),
                new DemoViewCache(),
                new DemoAutoload(),
            ]
        );

        $result = $this->normalizeContent($result);

        static::assertStringContainsString(
            $this->normalizeContent('Start to optimize you app.'),
            $result
        );
        static::assertStringContainsString(
            $this->normalizeContent('Optimize successed.'),
            $result
        );
    }

    protected function initContainerService(IContainer $container): void
    {
        $app = new AppForProduction($container, '');
        $this->assertInstanceof(IApp::class, $app);
        $container->singleton(IApp::class, $app);
    }
}

class AppForProduction extends Apps
{
    protected function registerBaseProvider(): void
    {
    }
}

class DemoRouterCache extends Command
{
    protected string $name = 'router:cache';

    protected string $description = 'This is a demo command';

    public function handle(): int
    {
        return 0;
    }
}

class DemoOptionCache extends Command
{
    protected string $name = 'option:cache';

    protected string $description = 'This is a demo command';

    public function handle(): int
    {
        return 0;
    }
}

class DemoI18nCache extends Command
{
    protected string $name = 'i18n:cache';

    protected string $description = 'This is a demo command';

    public function handle(): int
    {
        return 0;
    }
}

class DemoViewCache extends Command
{
    protected string $name = 'view:cache';

    protected string $description = 'This is a demo command';

    public function handle(): int
    {
        return 0;
    }
}

class DemoAutoload extends Command
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
