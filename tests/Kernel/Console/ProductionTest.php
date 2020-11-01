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
use Leevel\Kernel\Console\Production;
use Leevel\Kernel\IApp;
use Tests\Console\BaseCommand;
use Tests\TestCase;

class ProductionTest extends TestCase
{
    use BaseCommand;

    public function testBaseUse(): void
    {
        $result = $this->runCommand(
            new Production(),
            [
                'command' => 'production',
            ],
            function ($container) {
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

        $this->assertStringContainsString(
            $this->normalizeContent('Start to optimize you app.'),
            $result
        );
        $this->assertStringContainsString(
            $this->normalizeContent('Optimize successed.'),
            $result
        );
    }

    protected function initContainerService(IContainer $container): void
    {
        // 注册 app
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
