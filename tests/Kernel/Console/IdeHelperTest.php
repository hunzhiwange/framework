<?php

declare(strict_types=1);

namespace Tests\Kernel\Console;

use Leevel\Di\IContainer;
use Leevel\Kernel\App as Apps;
use Leevel\Kernel\Console\IdeHelper;
use Leevel\Kernel\IApp;
use Tests\Console\BaseCommand;
use Tests\Kernel\Utils\Assert\DemoClass;
use Tests\TestCase;

final class IdeHelperTest extends TestCase
{
    use BaseCommand;

    public function testBaseUse(): void
    {
        $result = '';
        $outResult = $this->obGetContents(function () use (&$result): void {
            $result = $this->runCommand(
                new IdeHelper(),
                [
                    'command' => 'make:idehelper',
                    'path' => DemoClass::class,
                ],
                function ($container): void {
                    $this->initContainerService($container);
                }
            );
        });

        $result = $this->normalizeContent($result);
        $outResult = $this->normalizeContent($outResult);

        static::assertStringContainsString(
            $this->normalizeContent(sprintf('Ide helper for class %s generate succeed.', DemoClass::class)),
            $result,
        );
        static::assertStringContainsString(
            $this->normalizeContent('* @method static void Demo1()'),
            $outResult,
        );
        static::assertStringContainsString(
            $this->normalizeContent('* @method static void Demo2(string $hello, int $world)'),
            $outResult,
        );
        static::assertStringContainsString(
            $this->normalizeContent('* @method static string Demo3(string $hello, ?int $world = null) demo3'),
            $outResult,
        );
        static::assertStringContainsString(
            $this->normalizeContent('* @method static void Demo4(...$hello)'),
            $outResult,
        );
    }

    protected function initContainerService(IContainer $container): void
    {
        $app = new AppForIdeHelper($container, '');
        $this->assertInstanceof(IApp::class, $app);
        $container->singleton(IApp::class, $app);
    }
}

class AppForIdeHelper extends Apps
{
    protected function registerBaseProvider(): void
    {
    }
}
