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

namespace Tests\I18n\Console;

use Leevel\Di\IContainer;
use Leevel\I18n\Console\Clear;
use Leevel\Kernel\IProject;
use Leevel\Option\IOption;
use Tests\Console\BaseCommand;
use Tests\TestCase;

/**
 * clear test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.07.18
 *
 * @version 1.0
 */
class ClearTest extends TestCase
{
    use BaseCommand;

    public function testBaseUse()
    {
        $cacheFile = __DIR__.'/i18n_clear.php';

        file_put_contents($cacheFile, 'foo');

        $result = $this->runCommand(
            new Clear(),
            [
                'command' => 'i18n:clear',
            ],
            function ($container) use ($cacheFile) {
                $this->initContainerService($container, $cacheFile);
            }
        );

        $this->assertContains(sprintf('I18n file %s cache clear successed.', $cacheFile), $result);

        $this->assertNotContains(sprintf('I18n cache files have been cleaned up.', $cacheFile), $result);
    }

    public function testHaveCleanedUp()
    {
        $cacheFile = __DIR__.'/i18n_clear2.php';

        $result = $this->runCommand(
            new Clear(),
            [
                'command' => 'i18n:clear',
            ],
            function ($container) use ($cacheFile) {
                $this->initContainerService($container, $cacheFile);
            }
        );

        $this->assertContains(sprintf('I18n file %s cache clear successed.', $cacheFile), $result);

        $this->assertContains(sprintf('I18n cache files have been cleaned up.', $cacheFile), $result);
    }

    protected function initContainerService(IContainer $container, string $cacheFile)
    {
        // 注册 project
        $project = $this->createMock(IProject::class);

        $this->assertInstanceof(IProject::class, $project);

        $project->method('i18nCachedPath')->willReturn($cacheFile);
        $this->assertEquals($cacheFile, $project->i18nCachedPath('en-US'));

        $container->singleton(IProject::class, $project);

        // 注册 option
        $option = $this->createMock(IOption::class);

        $this->assertInstanceof(IOption::class, $option);

        $option->method('get')->willReturn('en-US');
        $this->assertEquals('en-US', $option->get());

        $container->singleton(IOption::class, $option);
    }
}
