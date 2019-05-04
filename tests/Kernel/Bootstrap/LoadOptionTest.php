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
 * (c) 2010-2019 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Kernel\Bootstrap;

use Leevel\Di\Container;
use Leevel\Di\IContainer;
use Leevel\Filesystem\Fso;
use Leevel\Kernel\App as Apps;
use Leevel\Kernel\Bootstrap\LoadOption;
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
    protected function tearDown(): void
    {
        $appPath = __DIR__.'/app';
        $runtimePath = $appPath.'/bootstrap';

        if (is_dir($runtimePath)) {
            Fso::deleteDirectory($runtimePath, true);
        }

        if (getenv('RUNTIME_ENVIRONMENT')) {
            putenv('RUNTIME_ENVIRONMENT=fooenv');
        }
    }

    public function testBaseUse()
    {
        $bootstrap = new LoadOption();

        $app = new App3($appPath = __DIR__.'/app');

        $this->assertInstanceof(IContainer::class, $app);
        $this->assertInstanceof(Container::class, $app);

        $this->assertSame($appPath.'/bootstrap/option.php', $app->optionCachedPath());
        $this->assertFalse($app->isCachedOption());
        $this->assertSame($appPath.'/option', $app->optionPath());

        $this->assertNull($bootstrap->handle($app, true));

        $option = $app->make('option');

        $this->assertSame('development', $option->get('environment'));
        $this->assertSame('bar', $option->get('demo\\foo'));
    }

    public function testWithRuntimeEnv()
    {
        putenv('RUNTIME_ENVIRONMENT=fooenv');

        $bootstrap = new LoadOption();

        $app = new App3($appPath = __DIR__.'/app');

        $this->assertInstanceof(IContainer::class, $app);
        $this->assertInstanceof(Container::class, $app);

        $this->assertSame($appPath.'/bootstrap/fooenv.php', $app->optionCachedPath());
        $this->assertFalse($app->isCachedOption());
        $this->assertSame($appPath.'/option', $app->optionPath());

        $this->assertNull($bootstrap->handle($app, true));

        $option = $app->make('option');

        $this->assertSame('testing', $option->get('environment'));
        $this->assertSame('bar', $option->get('demo\\foo'));

        putenv('RUNTIME_ENVIRONMENT=');
    }

    public function testWithRuntimeEnvNotFound()
    {
        $appPath = __DIR__.'/app';

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            sprintf('Env file `%s` was not found.', $appPath.'/.notfoundenv')
        );

        putenv('RUNTIME_ENVIRONMENT=notfoundenv');

        $bootstrap = new LoadOption();

        $app = new App3($appPath);

        $this->assertInstanceof(IContainer::class, $app);
        $this->assertInstanceof(Container::class, $app);

        $this->assertSame($appPath.'/bootstrap/notfoundenv.php', $app->optionCachedPath());
        $this->assertFalse($app->isCachedOption());
        $this->assertSame($appPath.'/option', $app->optionPath());

        $bootstrap->handle($app, true);
    }

    public function testLoadCached()
    {
        // 重置环境
        putenv('RUNTIME_ENVIRONMENT=');

        $bootstrap = new LoadOption();

        $app = new App3($appPath = __DIR__.'/app');

        $this->assertInstanceof(IContainer::class, $app);
        $this->assertInstanceof(Container::class, $app);

        $this->assertSame($appPath.'/bootstrap/option.php', $app->optionCachedPath());
        $this->assertFalse($app->isCachedOption());
        $this->assertSame($appPath.'/option', $app->optionPath());

        mkdir($appPath.'/bootstrap', 0777, true);
        file_put_contents($appPath.'/bootstrap/option.php', file_get_contents($appPath.'/assert/option.php'));

        $this->assertTrue($app->isCachedOption());

        $this->assertNull($bootstrap->handle($app, true));

        $option = $app->make('option');

        $this->assertSame('development', $option->get('environment'));
        $this->assertSame('bar', $option->get('demo\\foo'));
        $this->assertNull($option->get('_env.foo'));
        $this->assertTrue($option->get('_env.debug'));
    }

    protected function tearUp()
    {
        $this->tearDown();
    }
}

class App3 extends Apps
{
    protected function registerBaseProvider(): void
    {
    }
}
