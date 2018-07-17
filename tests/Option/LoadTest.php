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

namespace Tests\Option;

use Leevel\Kernel\IProject;
use Leevel\Option\Load;
use Tests\TestCase;

/**
 * load test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.07.17
 *
 * @version 1.0
 */
class LoadTest extends TestCase
{
    public function testBaseUse()
    {
        $appPath = __DIR__.'/app1';

        $project = $this->createMock(IProject::class);

        $this->assertInstanceof(IProject::class, $project);

        $project->method('path')->willReturn($appPath);
        $this->assertEquals($appPath, $project->path());

        $project->method('pathEnv')->willReturn($appPath);
        $this->assertEquals($appPath, $project->pathEnv());

        $project->method('envFile')->willReturn('.env');
        $this->assertEquals('.env', $project->envFile());

        $options = ($load = new Load($appPath.'/option'))->loadData($project);

        // 多次调用会存在缓存
        $optionCaches = $load->loadData($project);

        $data = <<<'eot'
array (
  'app' => 
  array (
    'environment' => 'development',
    'debug' => false,
    '_env' => 
    array (
      'environment' => 'development',
      'debug' => 'true',
      'app_auth_key' => '7becb888f518b20224a988906df51e05',
    ),
    '_deferred_providers' => 
    array (
      0 => 
      array (
        'caches' => 'Leevel\\Cache\\Provider\\Register',
        'cache' => 'Leevel\\Cache\\Provider\\Register',
        'cache.load' => 'Leevel\\Cache\\Provider\\Register',
        'databases' => 'Leevel\\Database\\Provider\\Register',
        'database' => 'Leevel\\Database\\Provider\\Register',
        'encryption' => 'Leevel\\Encryption\\Provider\\Register',
        'filesystems' => 'Leevel\\Filesystem\\Provider\\Register',
        'filesystem' => 'Leevel\\Filesystem\\Provider\\Register',
        'view' => 'Leevel\\Mvc\\Provider\\Register',
        'page' => 'Leevel\\Page\\Provider\\Register',
        'swoole.default.server' => 'Leevel\\Protocol\\Provider\\Register',
        'swoole.http.server' => 'Leevel\\Protocol\\Provider\\Register',
        'swoole.websocket.server' => 'Leevel\\Protocol\\Provider\\Register',
        'swoole.rpc.server' => 'Leevel\\Protocol\\Provider\\Register',
        'throttler' => 'Leevel\\Throttler\\Provider\\Register',
        'Leevel\\Throttler\\Middleware\\Throttler' => 'Leevel\\Throttler\\Provider\\Register',
      ),
      1 => 
      array (
        'Leevel\\Cache\\Provider\\Register' => 
        array (
          'caches' => 
          array (
            0 => 'Leevel\\Cache\\Manager',
          ),
          'cache' => 
          array (
            0 => 'Leevel\\Cache\\Cache',
            1 => 'Leevel\\Cache\\ICache',
          ),
          'cache.load' => 
          array (
            0 => 'Leevel\\Cache\\Load',
          ),
        ),
        'Leevel\\Database\\Provider\\Register' => 
        array (
          'databases' => 
          array (
            0 => 'Leevel\\Database\\Manager',
          ),
          'database' => 
          array (
            0 => 'Leevel\\Database\\Database',
            1 => 'Leevel\\Database\\IDatabase',
          ),
        ),
        'Leevel\\Encryption\\Provider\\Register' => 
        array (
          'encryption' => 
          array (
            0 => 'Leevel\\Encryption\\Encryption',
            1 => 'Leevel\\Encryption\\IEncryption',
          ),
        ),
        'Leevel\\Filesystem\\Provider\\Register' => 
        array (
          'filesystems' => 
          array (
            0 => 'Leevel\\Filesystem\\Manager',
          ),
          'filesystem' => 
          array (
            0 => 'Leevel\\Filesystem\\Filesystem',
            1 => 'Leevel\\Filesystem\\IFilesystem',
          ),
        ),
        'Leevel\\Mvc\\Provider\\Register' => 
        array (
          'view' => 
          array (
            0 => 'Leevel\\Mvc\\View',
            1 => 'Leevel\\Mvc\\IView',
          ),
        ),
        'Leevel\\Page\\Provider\\Register' => 
        array (
          'page' => 
          array (
            0 => 'Leevel\\Page\\IPageFactory',
            1 => 'Leevel\\Page\\PageFactory',
          ),
        ),
        'Leevel\\Queue\\Provider\\Register' => 
        array (
        ),
        'Leevel\\Protocol\\Provider\\Register' => 
        array (
          'swoole.default.server' => 
          array (
            0 => 'Leevel\\Protocol\\Server',
          ),
          'swoole.http.server' => 
          array (
            0 => 'Leevel\\Protocol\\Http\\Server',
          ),
          'swoole.websocket.server' => 
          array (
            0 => 'Leevel\\Protocol\\Websocket\\Server',
          ),
          'swoole.rpc.server' => 
          array (
            0 => 'Leevel\\Protocol\\RpcServer',
          ),
        ),
        'Leevel\\Throttler\\Provider\\Register' => 
        array (
          'throttler' => 
          array (
            0 => 'Leevel\\Throttler\\Throttler',
            1 => 'Leevel\\Throttler\\IThrottler',
          ),
          0 => 'Leevel\\Throttler\\Middleware\\Throttler',
        ),
      ),
    ),
    '_composer' => 
    array (
      'providers' => 
      array (
        0 => 'Leevel\\Auth\\Provider\\Register',
        1 => 'Leevel\\Cookie\\Provider\\Register',
        2 => 'Leevel\\Mail\\Provider\\Register',
        3 => 'Leevel\\Session\\Provider\\Register',
        4 => 'Leevel\\Validate\\Provider\\Register',
        5 => 'Leevel\\View\\Provider\\Register',
        6 => 'Demo\\Provider\\Register',
        7 => 'Common\\Infra\\Provider\\Event',
        8 => 'Common\\Infra\\Provider\\Router',
      ),
      'ignores' => 
      array (
        0 => 'Leevel\\Notexits\\Provider\\Register',
      ),
      'commands' => 
      array (
        0 => 'Leevel\\Database\\Console',
        1 => 'Leevel\\I18n\\Console',
        2 => 'Leevel\\Mvc\\Console',
        3 => 'Leevel\\Queue\\Console',
        4 => 'Leevel\\Router\\Console',
        5 => 'Leevel\\Protocol\\Console',
        6 => 'Leevel\\Option\\Console',
        7 => 'Demo\\Demo\\Console',
        8 => 'Common\\App\\Console',
      ),
      'options' => 
      array (
        'demo' => 'option/extend/test.php',
      ),
      'i18ns' => 
      array (
        0 => 'i18n/extend',
      ),
      'metas' => 
      array (
        'foo' => 'bar',
      ),
    ),
  ),
  'demo' => 
  array (
    'foo' => 'bar',
  ),
)
eot;

        $this->assertSame(
            $data,
            $this->varExport(
                $options
            )
        );

        $this->assertSame(
            $data,
            $this->varExport(
                $optionCaches
            )
        );
    }

    public function testLoadDirNotExists()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'Option load dir is not exits.'
        );

        new Load(__DIR__.'/optionNotExists');
    }

    public function testAppNotFound()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'Unable to load the app option file.'
        );

        $appPath = __DIR__.'/app2';

        $project = $this->createMock(IProject::class);

        $this->assertInstanceof(IProject::class, $project);

        $project->method('path')->willReturn($appPath);
        $this->assertEquals($appPath, $project->path());

        $project->method('pathEnv')->willReturn($appPath);
        $this->assertEquals($appPath, $project->pathEnv());

        $project->method('envFile')->willReturn('.env');
        $this->assertEquals('.env', $project->envFile());

        $options = ($load = new Load($appPath.'/option'))->loadData($project);
    }

    public function testMergeComposerOptionException()
    {
        $appPath = __DIR__.'/app3';

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            sprintf('Option file %s is not exist.', $appPath.'/option/extend/test.php')
        );

        $project = $this->createMock(IProject::class);

        $this->assertInstanceof(IProject::class, $project);

        $project->method('path')->willReturn($appPath);
        $this->assertEquals($appPath, $project->path());

        $project->method('pathEnv')->willReturn($appPath);
        $this->assertEquals($appPath, $project->pathEnv());

        $project->method('envFile')->willReturn('.env');
        $this->assertEquals('.env', $project->envFile());

        $options = ($load = new Load($appPath.'/option'))->loadData($project);
    }

    public function testEnvException()
    {
        $appPath = __DIR__.'/app1';

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            sprintf('Unable to read the environment file at %s.', $appPath.'/.env.notexist')
        );

        $project = $this->createMock(IProject::class);

        $this->assertInstanceof(IProject::class, $project);

        $project->method('path')->willReturn($appPath);
        $this->assertEquals($appPath, $project->path());

        $project->method('pathEnv')->willReturn($appPath);
        $this->assertEquals($appPath, $project->pathEnv());

        $project->method('envFile')->willReturn('.env.notexist');
        $this->assertEquals('.env.notexist', $project->envFile());

        $options = ($load = new Load($appPath.'/option'))->loadData($project);
    }

    public function testEnvException2()
    {
        $appPath = __DIR__.'/app1';

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'Dotenv values containing spaces must be surrounded by quotes.'
        );

        $project = $this->createMock(IProject::class);

        $this->assertInstanceof(IProject::class, $project);

        $project->method('path')->willReturn($appPath);
        $this->assertEquals($appPath, $project->path());

        $project->method('pathEnv')->willReturn($appPath);
        $this->assertEquals($appPath, $project->pathEnv());

        $project->method('envFile')->willReturn('.env.wrong');
        $this->assertEquals('.env.wrong', $project->envFile());

        $options = ($load = new Load($appPath.'/option'))->loadData($project);
    }
}
