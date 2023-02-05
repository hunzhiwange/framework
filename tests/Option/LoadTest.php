<?php

declare(strict_types=1);

namespace Tests\Option;

use Leevel\Kernel\IApp;
use Leevel\Option\Load;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
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

        $options = ($load = new Load($appPath.'/option'))->loadData($app);

        // 多次调用会存在缓存
        $optionCaches = $load->loadData($app);

        $data = file_get_contents(__DIR__.'/app1/option.json');

        static::assertSame(
            trim($data),
            $this->varJson(
                $options
            )
        );

        static::assertSame(
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
        static::assertSame($appPath, $app->path());

        $app->method('envPath')->willReturn($appPath);
        static::assertSame($appPath, $app->envPath());

        $app->method('envFile')->willReturn('.env');
        static::assertSame('.env', $app->envFile());

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
        static::assertSame($appPath, $app->path());

        $app->method('envPath')->willReturn($appPath);
        static::assertSame($appPath, $app->envPath());

        $app->method('envFile')->willReturn('.env');
        static::assertSame('.env', $app->envFile());

        $options = ($load = new Load($appPath.'/option'))->loadData($app);
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

        $options = ($load = new Load($appPath.'/option'))->loadData($app);
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

        $options = ($load = new Load($appPath.'/option'))->loadData($app);
    }

    public function testMergeComposerOptionNewKey(): void
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

        $options = ($load = new Load($appPath.'/option'))->loadData($app);

        $data = file_get_contents(__DIR__.'/app5/option.json');

        static::assertSame(
            trim($data),
            $this->varJson(
                $options
            )
        );
    }
}
