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

namespace Tests\Option;

use Leevel\Kernel\IApp;
use Leevel\Option\Load;
use Tests\TestCase;

class LoadTest extends TestCase
{
    public function testBaseUse(): void
    {
        $appPath = __DIR__.'/app1';

        $app = $this->createMock(IApp::class);

        $this->assertInstanceof(IApp::class, $app);

        $app->method('path')->willReturn($appPath);
        $this->assertEquals($appPath, $app->path());

        $app->method('envPath')->willReturn($appPath);
        $this->assertEquals($appPath, $app->envPath());

        $app->method('envFile')->willReturn('.env');
        $this->assertEquals('.env', $app->envFile());

        $options = ($load = new Load($appPath.'/option'))->loadData($app);

        // 多次调用会存在缓存
        $optionCaches = $load->loadData($app);

        $data = file_get_contents(__DIR__.'/app1/option.json');

        $this->assertSame(
            trim($data),
            $this->varJson(
                $options
            )
        );

        $this->assertSame(
            trim($data),
            $this->varJson(
                $optionCaches
            )
        );
    }

    public function testLoadDirNotExists(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            sprintf('Option load dir %s is not exits.', __DIR__.'/optionNotExists')
        );

        new Load(__DIR__.'/optionNotExists');
    }

    public function testAppNotFound(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'Unable to load the app option file.'
        );

        $appPath = __DIR__.'/app2';

        $app = $this->createMock(IApp::class);

        $this->assertInstanceof(IApp::class, $app);

        $app->method('path')->willReturn($appPath);
        $this->assertEquals($appPath, $app->path());

        $app->method('envPath')->willReturn($appPath);
        $this->assertEquals($appPath, $app->envPath());

        $app->method('envFile')->willReturn('.env');
        $this->assertEquals('.env', $app->envFile());

        $options = ($load = new Load($appPath.'/option'))->loadData($app);
    }

    public function testMergeComposerOptionException(): void
    {
        $appPath = __DIR__.'/app3';

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            sprintf('Option file %s is not exist.', $appPath.'/option/extend/test.php')
        );

        $app = $this->createMock(IApp::class);

        $this->assertInstanceof(IApp::class, $app);

        $app->method('path')->willReturn($appPath);
        $this->assertEquals($appPath, $app->path());

        $app->method('envPath')->willReturn($appPath);
        $this->assertEquals($appPath, $app->envPath());

        $app->method('envFile')->willReturn('.env');
        $this->assertEquals('.env', $app->envFile());

        $options = ($load = new Load($appPath.'/option'))->loadData($app);
    }

    public function testEnvException(): void
    {
        $appPath = __DIR__.'/app1';

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            sprintf('Unable to read the environment file at %s.', $appPath.'/.env.notexist')
        );

        $app = $this->createMock(IApp::class);

        $this->assertInstanceof(IApp::class, $app);

        $app->method('path')->willReturn($appPath);
        $this->assertEquals($appPath, $app->path());

        $app->method('envPath')->willReturn($appPath);
        $this->assertEquals($appPath, $app->envPath());

        $app->method('envFile')->willReturn('.env.notexist');
        $this->assertEquals('.env.notexist', $app->envFile());

        $options = ($load = new Load($appPath.'/option'))->loadData($app);
    }

    public function testEnvException2(): void
    {
        $appPath = __DIR__.'/app1';

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'Dotenv values containing spaces must be surrounded by quotes.'
        );

        $app = $this->createMock(IApp::class);

        $this->assertInstanceof(IApp::class, $app);

        $app->method('path')->willReturn($appPath);
        $this->assertEquals($appPath, $app->path());

        $app->method('envPath')->willReturn($appPath);
        $this->assertEquals($appPath, $app->envPath());

        $app->method('envFile')->willReturn('.env.wrong');
        $this->assertEquals('.env.wrong', $app->envFile());

        $options = ($load = new Load($appPath.'/option'))->loadData($app);
    }

    public function testMergeComposerOptionNewKey(): void
    {
        $appPath = __DIR__.'/app5';

        $app = $this->createMock(IApp::class);

        $this->assertInstanceof(IApp::class, $app);

        $app->method('path')->willReturn($appPath);
        $this->assertEquals($appPath, $app->path());

        $app->method('envPath')->willReturn($appPath);
        $this->assertEquals($appPath, $app->envPath());

        $app->method('envFile')->willReturn('.env');
        $this->assertEquals('.env', $app->envFile());

        $options = ($load = new Load($appPath.'/option'))->loadData($app);

        $data = file_get_contents(__DIR__.'/app5/option.json');

        $this->assertSame(
            trim($data),
            $this->varJson(
                $options
            )
        );
    }
}
