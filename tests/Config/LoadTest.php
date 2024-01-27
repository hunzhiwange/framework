<?php

declare(strict_types=1);

namespace Tests\Config;

use Leevel\Config\Load;
use Leevel\Kernel\IApp;
use Tests\TestCase;

final class LoadTest extends TestCase
{
    public function testBaseUse(): void
    {
        $appPath = __DIR__.'/app1';

        $app = $this->createMock(IApp::class);
        $this->assertInstanceof(IApp::class, $app);

        $app->method('path')->willReturn($appPath);
        static::assertSame($appPath, $app->path());

        $app->method('envPath')->willReturn($appPath);
        static::assertSame($appPath, $app->envPath());

        $app->method('envFile')->willReturn('.env');
        static::assertSame('.env', $app->envFile());

        $configs = ($load = new Load($appPath.'/config'))->loadData($app);

        // 多次调用会存在缓存
        $configCaches = $load->loadData($app);

        $data = file_get_contents(__DIR__.'/app1/config.json');

        static::assertSame(
            trim($data),
            $this->varJson(
                $configs
            )
        );

        static::assertSame(
            trim($data),
            $this->varJson(
                $configCaches
            )
        );
    }

    public function testLoadDirNotExists(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            sprintf('Config load dir %s is not exits.', __DIR__.'/configNotExists')
        );

        new Load(__DIR__.'/configNotExists');
    }

    public function testAppNotFound(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'Unable to load the app config file.'
        );

        $appPath = __DIR__.'/app2';

        $app = $this->createMock(IApp::class);
        $this->assertInstanceof(IApp::class, $app);

        $app->method('path')->willReturn($appPath);
        static::assertSame($appPath, $app->path());

        $app->method('envPath')->willReturn($appPath);
        static::assertSame($appPath, $app->envPath());

        $app->method('envFile')->willReturn('.env');
        static::assertSame('.env', $app->envFile());

        (new Load($appPath.'/config'))->loadData($app);
    }

    public function testMergeComposerConfigException(): void
    {
        $appPath = __DIR__.'/app3';

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            sprintf('Config file %s is not exist.', $appPath.'/config/extend/test.php')
        );

        $app = $this->createMock(IApp::class);

        $this->assertInstanceof(IApp::class, $app);

        $app->method('path')->willReturn($appPath);
        static::assertSame($appPath, $app->path());

        $app->method('envPath')->willReturn($appPath);
        static::assertSame($appPath, $app->envPath());

        $app->method('envFile')->willReturn('.env');
        static::assertSame('.env', $app->envFile());

        (new Load($appPath.'/config'))->loadData($app);
    }

    public function testEnvException(): void
    {
        $appPath = __DIR__.'/app1';

        $this->expectException(\Dotenv\Exception\InvalidPathException::class);
        $this->expectExceptionMessage(
            sprintf('Unable to read any of the environment file(s) at [%s].', $appPath.'/.env.notexist')
        );

        $app = $this->createMock(IApp::class);
        $this->assertInstanceof(IApp::class, $app);

        $app->method('path')->willReturn($appPath);
        static::assertSame($appPath, $app->path());

        $app->method('envPath')->willReturn($appPath);
        static::assertSame($appPath, $app->envPath());

        $app->method('envFile')->willReturn('.env.notexist');
        static::assertSame('.env.notexist', $app->envFile());

        (new Load($appPath.'/config'))->loadData($app);
    }

    public function testEnvException2(): void
    {
        $appPath = __DIR__.'/app1';

        $this->expectException(\Dotenv\Exception\InvalidFileException::class);
        $this->expectExceptionMessage(
            'Failed to parse dotenv file. Encountered unexpected whitespace at [with spaces].'
        );

        $app = $this->createMock(IApp::class);

        $this->assertInstanceof(IApp::class, $app);

        $app->method('path')->willReturn($appPath);
        static::assertSame($appPath, $app->path());

        $app->method('envPath')->willReturn($appPath);
        static::assertSame($appPath, $app->envPath());

        $app->method('envFile')->willReturn('.env.wrong');
        static::assertSame('.env.wrong', $app->envFile());

        (new Load($appPath.'/config'))->loadData($app);
    }

    public function testMergeComposerConfigNewKey(): void
    {
        $appPath = __DIR__.'/app5';

        $app = $this->createMock(IApp::class);
        $this->assertInstanceof(IApp::class, $app);

        $app->method('path')->willReturn($appPath);
        static::assertSame($appPath, $app->path());

        $app->method('envPath')->willReturn($appPath);
        static::assertSame($appPath, $app->envPath());

        $app->method('envFile')->willReturn('.env');
        static::assertSame('.env', $app->envFile());

        $configs = (new Load($appPath.'/config'))->loadData($app);

        $data = file_get_contents(__DIR__.'/app5/config.json');

        static::assertSame(
            trim($data),
            $this->varJson(
                $configs
            )
        );
    }

    public function test1(): void
    {
        $appPath = __DIR__.'/app6';

        $app = $this->createMock(IApp::class);
        $this->assertInstanceof(IApp::class, $app);

        $app->method('path')->willReturn($appPath);
        static::assertSame($appPath, $app->path());

        $app->method('envPath')->willReturn($appPath);
        static::assertSame($appPath, $app->envPath());

        $app->method('envFile')->willReturn('.env');
        static::assertSame('.env', $app->envFile());

        (new Load($appPath.'/config'))->loadData($app);
    }
}
