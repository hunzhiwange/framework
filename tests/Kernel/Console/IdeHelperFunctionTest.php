<?php

declare(strict_types=1);

namespace Tests\Kernel\Console;

use Leevel\Di\IContainer;
use Leevel\Kernel\App as Apps;
use Leevel\Kernel\Console\IdeHelperFunction;
use Leevel\Kernel\IApp;
use Tests\Console\BaseCommand;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class IdeHelperFunctionTest extends TestCase
{
    use BaseCommand;

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
