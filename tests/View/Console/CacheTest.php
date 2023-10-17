<?php

declare(strict_types=1);

namespace Tests\View\Console;

use Leevel\Di\IContainer;
use Leevel\Filesystem\Helper;
use Leevel\Kernel\App as Apps;
use Leevel\Kernel\IApp;
use Leevel\Option\Option;
use Leevel\View\Console\Cache;
use Leevel\View\Manager;
use Tests\Console\BaseCommand;
use Tests\TestCase;

/**
 * @internal
 */
final class CacheTest extends TestCase
{
    use BaseCommand;

    protected function tearDown(): void
    {
        if (is_dir($cacheDirPath = __DIR__.'/cache_app')) {
            Helper::deleteDirectory($cacheDirPath);
        }
    }

    public function testBaseUse(): void
    {
        $result = '';
        $this->obGetContents(function () use (&$result): void {
            $result = $this->runCommand(
                new Cache(),
                [
                    'command' => 'view:cache',
                ],
                function ($container): void {
                    $this->initContainerService($container);
                }
            );
        });

        $result = $this->normalizeContent($result);
        static::assertStringContainsString(
            $this->normalizeContent('Start to cache view.'),
            $result,
        );
        static::assertStringContainsString(
            $this->normalizeContent(sprintf('Start to compiles path `%s`', __DIR__.'/assert')),
            $result,
        );
        static::assertStringContainsString(
            $this->normalizeContent('View files cache succeed.'),
            $result,
        );

        static::assertDirectoryExists(__DIR__.'/cache_app/themes');
    }

    protected function initContainerService(IContainer $container): void
    {
        $app = new AppForCache($container, '');
        $this->assertInstanceof(IApp::class, $app);
        $container->singleton('app', $app);
        $container->alias('app', IApp::class);
        $this->makeViewViews($container);
    }

    protected function makeViewViews(IContainer $container): void
    {
        $option = new Option([
            'view' => [
                'default' => 'html',
                'action_fail' => 'public/fail',
                'action_success' => 'public/success',
                'connect' => [
                    'html' => [
                        'driver' => 'html',
                        'suffix' => '.html',
                    ],
                    'phpui' => [
                        'driver' => 'phpui',
                        'suffix' => '.php',
                    ],
                ],
            ],
        ]);
        $container->singleton('option', $option);

        $container
            ->singleton(
                'views',
                fn (IContainer $container): Manager => new Manager($container),
            )
        ;
    }
}

class AppForCache extends Apps
{
    public function themesPath(string $path = ''): string
    {
        return __DIR__.'/assert';
    }

    public function storagePath(string $path = ''): string
    {
        return __DIR__.'/cache_'.$path;
    }

    protected function registerBaseProvider(): void
    {
    }
}
