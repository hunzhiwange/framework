<?php

declare(strict_types=1);

namespace Tests\Kernel\Console;

use Leevel\Di\IContainer;
use Leevel\Filesystem\Helper;
use Leevel\Kernel\App as Apps;
use Leevel\Kernel\Console\IdeHelperFunction;
use Leevel\Kernel\IApp;
use Tests\Console\BaseCommand;
use Tests\TestCase;

final class IdeHelperFunctionTest extends TestCase
{
    use BaseCommand;

    protected function setUp(): void
    {
        $this->tearDown();
    }

    protected function tearDown(): void
    {
        $cacheDir = \dirname(__DIR__).'/Utils/Assert/cache/';
        if (is_dir($cacheDir)) {
            Helper::deleteDirectory($cacheDir);
        }
    }

    public function testBaseUse(): void
    {
        $result = '';
        $dirName = \dirname(__DIR__).'/Utils/Assert/Helper';
        $outResult = $this->obGetContents(function () use (&$result, $dirName): void {
            $result = $this->runCommand(
                new IdeHelperFunction(),
                [
                    'command' => 'make:idehelper:function',
                    'dir' => $dirName,
                ],
                function ($container): void {
                    $this->initContainerService($container);
                }
            );
        });

        $result = $this->normalizeContent($result);
        $outResult = $this->normalizeContent($outResult);

        static::assertStringContainsString(
            $this->normalizeContent(sprintf('Ide helper for functions of dir %s generate succeed.', $dirName)),
            $result,
        );

        static::assertStringContainsString(
            $this->normalizeContent('* @method static void demo1()'),
            $outResult,
        );
        static::assertStringContainsString(
            $this->normalizeContent('* @method static void demo2(string $hello, int $world)'),
            $outResult,
        );
        static::assertStringContainsString(
            $this->normalizeContent('* @method static string demo3(string $hello, ?int $world = null) demo3'),
            $outResult,
        );
        static::assertStringContainsString(
            $this->normalizeContent('* @method static void demo4(...$hello)'),
            $outResult,
        );
        static::assertStringContainsString(
            $this->normalizeContent('* @method static void demoHelloWorld()'),
            $outResult,
        );
    }

    public function test1(): void
    {
        $result = '';
        $dirName = \dirname(__DIR__).'/Utils/Assert/HelperNotFound';
        $this->obGetContents(function () use (&$result, $dirName): void {
            $result = $this->runCommand(
                new IdeHelperFunction(),
                [
                    'command' => 'make:idehelper:function',
                    'dir' => $dirName,
                ],
                function ($container): void {
                    $this->initContainerService($container);
                }
            );
        });

        $result = $this->normalizeContent($result);

        static::assertStringContainsString(
            $this->normalizeContent(sprintf('Dir `%s` is not exits.', $dirName)),
            $result,
        );
    }

    public function test2(): void
    {
        $result = '';
        $dirName = \dirname(__DIR__).'/Utils/Assert/Helper';
        $cachePath = \dirname(__DIR__).'/Utils/Assert/cache/cache123.php';
        $this->obGetContents(function () use (&$result, $dirName, $cachePath): void {
            $result = $this->runCommand(
                new IdeHelperFunction(),
                [
                    'command' => 'make:idehelper:function',
                    'dir' => $dirName,
                    '--cachepath' => $cachePath,
                ],
                function ($container): void {
                    $this->initContainerService($container);
                }
            );
        });

        $result = $this->normalizeContent($result);

        static::assertStringContainsString(
            $this->normalizeContent(sprintf('Ide helper for functions of dir %s generate succeed.', $dirName)),
            $result,
        );

        static::assertStringContainsString(
            $this->normalizeContent(sprintf('Ide helper cache succeed at %s.', $cachePath)),
            $result,
        );
    }

    protected function initContainerService(IContainer $container): void
    {
        $app = new AppForIdeHelperFunction($container, '');
        $this->assertInstanceof(IApp::class, $app);
        $container->singleton(IApp::class, $app);
    }
}

class AppForIdeHelperFunction extends Apps
{
    protected function registerBaseProvider(): void
    {
    }
}
