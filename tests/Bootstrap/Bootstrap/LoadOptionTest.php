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

namespace Tests\Bootstrap\Bootstrap;

use Leevel\Bootstrap\Bootstrap\LoadOption;
use Leevel\Bootstrap\Project as Projects;
use Leevel\Di\Container;
use Leevel\Di\IContainer;
use Leevel\Filesystem\Fso;
use Tests\TestCase;

/**
 * loadOption test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.08.20
 *
 * @version 1.0
 */
class LoadOptionTest extends TestCase
{
    public function testBaseUse()
    {
        $_ENV = [];

        $bootstrap = new LoadOption();

        $project = new Project3($appPath = __DIR__.'/app');

        $this->assertInstanceof(IContainer::class, $project);
        $this->assertInstanceof(Container::class, $project);

        $this->assertSame($appPath.'/runtime/bootstrap/option.php', $project->optionCachedPath());
        $this->assertFalse($project->isCachedOption());
        $this->assertSame($appPath.'/option', $project->optionPath());

        $this->assertNull($bootstrap->handle($project, true));

        $option = $project->make('option');

        $this->assertSame('development', $option->get('environment'));
        $this->assertSame('bar', $option->get('demo\\foo'));

        $_ENV = [];
    }

    public function testLoadCached()
    {
        $_ENV = [];

        $bootstrap = new LoadOption();

        $project = new Project3($appPath = __DIR__.'/app');

        $this->assertInstanceof(IContainer::class, $project);
        $this->assertInstanceof(Container::class, $project);

        $this->assertSame($appPath.'/runtime/bootstrap/option.php', $project->optionCachedPath());
        $this->assertFalse($project->isCachedOption());
        $this->assertSame($appPath.'/option', $project->optionPath());

        mkdir($appPath.'/runtime/bootstrap', 0777, true);
        file_put_contents($appPath.'/runtime/bootstrap/option.php', file_get_contents($appPath.'/assert/option.php'));

        $this->assertTrue($project->isCachedOption());

        $this->assertNull($bootstrap->handle($project, true));

        $option = $project->make('option');

        $this->assertSame('development', $option->get('environment'));
        $this->assertSame('bar', $option->get('demo\\foo'));
        $this->assertNull($option->get('_env.foo'));
        $this->assertTrue($option->get('_env.debug'));

        Fso::deleteDirectory($appPath.'/runtime', true);

        $_ENV = [];
    }
}

class Project3 extends Projects
{
    protected function registerBaseProvider()
    {
    }
}
