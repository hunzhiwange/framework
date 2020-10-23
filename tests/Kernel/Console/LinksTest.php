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
use Leevel\Di\IContainer;
use Leevel\Filesystem\Helper;
use Leevel\Kernel\App as Apps;
use Leevel\Kernel\Console\Links;
use Leevel\Kernel\IApp;
use Tests\Console\BaseCommand;
use Tests\TestCase;

class LinksTest extends TestCase
{
    use BaseCommand;

    protected function setUp(): void
    {
        $this->tearDown();
    }

    protected function tearDown(): void
    {
        $dirs = [
            __DIR__.'/assert_new',
        ];
        foreach ($dirs as $dir) {
            Helper::deleteDirectory($dir);
        }
    }

    public function testBaseUse(): void
    {
        $result = $this->runCommand(
            new Links(),
            [
                'command' => 'links',
            ],
            function ($container) {
                $this->initContainerService($container);
            },
            [
                new DemoLinkApis(),
                new DemoLinkPublic(),
                new DemoLinkStorage(),
                new DemoLinkDebugbar(),
            ]
        );

        $result = $this->normalizeContent($result);

        $this->assertStringContainsString(
            $this->normalizeContent('Start to create symbolic links.'),
            $result
        );
        $this->assertStringContainsString(
            $this->normalizeContent('Links created successed.'),
            $result
        );
    }

    protected function initContainerService(IContainer $container): void
    {
        // 注册 app
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
        return 0;
    }
}

class DemoLinkPublic extends Command
{
    protected string $name = 'link:public';

    protected string $description = 'This is a demo command';

    public function handle(): int
    {
        return 0;
    }
}

class DemoLinkStorage extends Command
{
    protected string $name = 'link:storage';

    protected string $description = 'This is a demo command';

    public function handle(): int
    {
        return 0;
    }
}

class DemoLinkDebugbar extends Command
{
    protected string $name = 'link:debugbar';

    protected string $description = 'This is a demo command';

    public function handle(): int
    {
        return 0;
    }
}