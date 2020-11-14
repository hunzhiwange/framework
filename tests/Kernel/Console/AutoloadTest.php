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
