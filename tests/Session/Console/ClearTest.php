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

namespace Tests\Session\Console;

use Leevel\Di\IContainer;
use Leevel\Filesystem\Helper;
use Leevel\Kernel\IApp;
use Leevel\Session\Console\Clear;
use Tests\Console\BaseCommand;
use Tests\TestCase;

class ClearTest extends TestCase
{
    use BaseCommand;

    public function testBaseUse(): void
    {
        $cacheDir = __DIR__.'/session_clear';
        Helper::createFile($cacheDir.'/hello.txt', 'foo');
        $this->assertDirectoryExists($cacheDir);

        $result = $this->runCommand(
            new Clear(),
            [
                'command' => 'session:clear',
            ],
            function ($container) use ($cacheDir) {
                $this->initContainerService($container, $cacheDir);
            }
        );

        $this->assertDirectoryNotExists($cacheDir);
        $result = $this->normalizeContent($result);
        $this->assertStringContainsString(
            $this->normalizeContent('Start to clear cache session.'),
            $result
        );
        $this->assertStringContainsString(
            $this->normalizeContent(sprintf('Session cache files in path %s clear successed.', $cacheDir)),
            $result
        );
    }

    protected function initContainerService(IContainer $container, string $cacheDir): void
    {
        $app = $this->createMock(IApp::class);
        $this->assertInstanceof(IApp::class, $app);

        $app->method('runtimePath')->willReturn($cacheDir);
        $this->assertEquals($cacheDir, $app->runtimePath('session'));

        $container->singleton(IApp::class, $app);
    }
}
