<?php

declare(strict_types=1);

/*
 * This file is part of the ************************ package.
 * _____________                           _______________
 *  ______/     \__  _____  ____  ______  / /_  _________
 *   ____/ __   / / / / _ \/ __`\/ / __ \/ __ \/ __ \___
 *    __/ / /  / /_/ /  __/ /  \  / /_/ / / / / /_/ /__
 *      \_\ \_/\____/\___/_/   / / .___/_/ /_/ .___/
 *         \_\                /_/_/         /_/
 *
 * The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
 * (c) 2010-2020 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Kernel\Console;

use Leevel\Console\Command;
use Leevel\Console\Option;
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
        // 注册 app
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
                Option::VALUE_OPTIONAL,
                'Where is composer.',
                'composer',
            ],
            [
                'dev',
                '-d',
                Option::VALUE_NONE,
                'Without `--no-dev` option for `composer dump-autoload --optimize`.',
            ],
        ];
    }
}
