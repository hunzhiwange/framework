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

        $project->method('envPath')->willReturn($appPath);
        $this->assertEquals($appPath, $project->envPath());

        $project->method('envFile')->willReturn('.env');
        $this->assertEquals('.env', $project->envFile());

        $options = ($load = new Load($appPath.'/option'))->loadData($project);

        // 多次调用会存在缓存
        $optionCaches = $load->loadData($project);

        $data = file_get_contents(__DIR__.'/option.data');

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

        $project->method('envPath')->willReturn($appPath);
        $this->assertEquals($appPath, $project->envPath());

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

        $project->method('envPath')->willReturn($appPath);
        $this->assertEquals($appPath, $project->envPath());

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

        $project->method('envPath')->willReturn($appPath);
        $this->assertEquals($appPath, $project->envPath());

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

        $project->method('envPath')->willReturn($appPath);
        $this->assertEquals($appPath, $project->envPath());

        $project->method('envFile')->willReturn('.env.wrong');
        $this->assertEquals('.env.wrong', $project->envFile());

        $options = ($load = new Load($appPath.'/option'))->loadData($project);
    }
}
