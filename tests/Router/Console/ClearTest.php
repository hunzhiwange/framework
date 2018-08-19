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
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Router\Console;

use Leevel\Di\IContainer;
use Leevel\Kernel\IProject;
use Leevel\Router\Console\Clear;
use Tests\Console\BaseCommand;
use Tests\TestCase;

/**
 * clear test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.07.19
 *
 * @version 1.0
 */
class ClearTest extends TestCase
{
    use BaseCommand;

    public function testBaseUse()
    {
        $cacheFile = __DIR__.'/router_clear.php';

        file_put_contents($cacheFile, 'foo');

        $result = $this->runCommand(
            new Clear(),
            [
                'command' => 'router:clear',
            ],
            function ($container) use ($cacheFile) {
                $this->initContainerService($container, $cacheFile);
            }
        );

        $this->assertContains(
            sprintf('Router file %s cache clear successed.', $cacheFile),
            $result
        );

        $this->assertNotContains(
            sprintf('Router cache files have been cleaned up.', $cacheFile),
            $result
        );
    }

    public function testHaveCleanedUp()
    {
        $cacheFile = __DIR__.'/router_clear2.php';

        $result = $this->runCommand(
            new Clear(),
            [
                'command' => 'router:clear',
            ],
            function ($container) use ($cacheFile) {
                $this->initContainerService($container, $cacheFile);
            }
        );

        $this->assertContains(
            sprintf('Router file %s cache clear successed.', $cacheFile),
            $result
        );

        $this->assertContains(
            sprintf('Router cache files have been cleaned up.', $cacheFile),
            $result
        );
    }

    protected function initContainerService(IContainer $container, string $cacheFile)
    {
        $project = $this->createMock(IProject::class);

        $this->assertInstanceof(IProject::class, $project);

        $project->method('routerCachedPath')->willReturn($cacheFile);
        $this->assertEquals($cacheFile, $project->routerCachedPath());

        $container->singleton(IProject::class, $project);
    }
}
