<?php

declare(strict_types=1);

namespace Tests\I18n\Console;

use Leevel\Di\IContainer;
use Leevel\Filesystem\Helper;
use Leevel\I18n\Console\Generate;
use Leevel\Kernel\App as Apps;
use Leevel\Kernel\IApp;
use Leevel\Option\IOption;
use Leevel\Option\Option;
use Tests\Console\BaseCommand;
use Tests\TestCase;

final class GenerateTest extends TestCase
{
    use BaseCommand;

    protected function tearDown(): void
    {
        $path = __DIR__.'/../cachePath';
        if (is_dir($path)) {
            Helper::deleteDirectory($path);
        }
    }

    public function testBaseUse(): void
    {
        $cacheFile = \dirname(__DIR__).'/cachePath/assets/i18n/[i18n]/base.po';

        $result = $this->runCommand(
            new Generate(),
            [
                'command' => 'i18n:generate',
            ],
            function ($container): void {
                $this->initContainerService($container);
            }
        );

        $result = $this->normalizeContent($result);

        static::assertStringContainsString(
            $this->normalizeContent('Start to generate i18n file.'),
            $result
        );

        foreach (['zh-CN', 'en-US'] as $i18n) {
            static::assertStringContainsString(
                $this->normalizeContent(
                    sprintf(
                        'I18n file base generate succeed at %s.',
                        str_replace('[i18n]', $i18n, $cacheFile)
                    )
                ),
                $result
            );
        }

        static::assertStringContainsString(
            $this->normalizeContent('I18n file generate succeed.'),
            $result
        );
    }

    protected function initContainerService(IContainer $container): void
    {
        $app = new AppGenerate($container, '');
        $this->assertInstanceof(IApp::class, $app);
        $container->singleton(IApp::class, $app);

        // 注册 option
        $option = new Option([
            'app' => [
                ':composer' => [
                    'i18n-paths' => [
                        'base' => [
                            __DIR__.'/../assert/lang',
                        ],
                    ],
                ],
            ],
            'console' => [
                'app_i18n' => 'zh-CN,en-US',
            ],
        ]);

        $container->singleton('option', function () use ($option) {
            return $option;
        });
        $container->singleton(IOption::class, $option);
    }
}

class AppGenerate extends Apps
{
    public function path(string $path = ''): string
    {
        return \dirname(__DIR__).'/cachePath/'.$path;
    }
}
