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

namespace Tests\Database\Console;

use Leevel\Database\Console\Breakpoint;
use Leevel\Di\IContainer;
use Leevel\Kernel\IApp;
use Tests\Console\BaseCommand;
use Tests\TestCase;

class MigrateBreakpoint extends TestCase
{
    use BaseCommand;

    public function testBaseUse(): void
    {
        $result = $this->runCommand(
            new Breakpoint(),
            [
                'command'  => 'migrate:breakpoint',
                '--target' => '20191005015700',
            ],
            function ($container) {
                $this->initContainerService($container);
            }
        );

        $resultRemove = $this->runCommand(
            new Breakpoint(),
            [
                'command'  => 'migrate:breakpoint',
                '--target' => '20191005015700',
                '--unset'  => true,
            ],
            function ($container) {
                $this->initContainerService($container);
            }
        );

        $result = $this->normalizeContent($result);
        $resultRemove = $this->normalizeContent($resultRemove);

        $this->assertStringContainsString(
            $this->normalizeContent('using config file ./phinx.php'),
            $result
        );

        $this->assertStringContainsString(
            $this->normalizeContent('using config parser php'),
            $result
        );

        $this->assertStringContainsString(
            $this->normalizeContent('Breakpoint set for 20191005015700 RoleSoftDeleted'),
            $result
        );

        $this->assertStringContainsString(
            $this->normalizeContent('warning no environment specified, defaulting to: development'),
            $resultRemove
        );

        $this->assertStringContainsString(
            $this->normalizeContent('Breakpoint cleared for 20191005015700 RoleSoftDeleted'),
            $resultRemove
        );
    }

    protected function initContainerService(IContainer $container): void
    {
        $app = $this->createMock(IApp::class);
        $this->assertInstanceof(IApp::class, $app);

        $container->singleton(IApp::class, $app);
    }
}
