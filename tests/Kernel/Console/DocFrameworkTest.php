<?php

declare(strict_types=1);

namespace Tests\Kernel\Console;

use Leevel\Config\IConfig;
use Leevel\Di\IContainer;
use Leevel\Filesystem\Helper;
use Leevel\Kernel\App as Apps;
use Leevel\Kernel\Console\Doc;
use Leevel\Kernel\Console\DocFramework;
use Leevel\Kernel\IApp;
use Tests\Console\BaseCommand;
use Tests\TestCase;

final class DocFrameworkTest extends TestCase
{
    use BaseCommand;

    public function testBaseUse(): void
    {
        $result = '';
        $dirName = \dirname(__DIR__).'/Utils/Assert/Doc';
        $outputDirName = \dirname(__DIR__).'/Utils/Assert/Doc/Output{i18n}';
        $this->obGetContents(function () use (&$result, $dirName): void {
            $result = $this->runCommand(
                new DocFramework(),
                [
                    'command' => 'make:docwithin',
                    'path' => $dirName,
                ],
                function ($container): void {
                    $this->initContainerService($container);
                },
                [
                    new Doc(),
                ],
            );
        });

        $result = $this->normalizeContent($result);
        $outputDirNameDefault = str_replace('{i18n}', '', $outputDirName);
        $outputDirNameZhCn = str_replace('{i18n}', '/zh-CN', $outputDirName);
        $outputDirNameEnUs = str_replace('{i18n}', '/en-US', $outputDirName);

        self::assertStringContainsString(
            $this->normalizeContent(sprintf('Class Tests\\Kernel\\Utils\\Assert\\Doc\\Demo1 was generate succeed at %s/demo1.md.', $outputDirNameDefault)),
            $result,
        );
        self::assertStringContainsString(
            $this->normalizeContent(sprintf('Class Tests\\Kernel\\Utils\\Assert\\Doc\\Demo3 was generate succeed at %s/demo3.md.', $outputDirNameDefault)),
            $result,
        );
        self::assertStringContainsString(
            $this->normalizeContent(sprintf('Class Tests\\Kernel\\Utils\\Assert\\Doc\\Demo2 was generate succeed at %s/demo2.md.', $outputDirNameDefault)),
            $result,
        );
        self::assertStringContainsString(
            $this->normalizeContent(sprintf('Class Tests\\Kernel\\Utils\\Assert\\Doc\\Demo1 was generate succeed at %s/demo1.md.', $outputDirNameZhCn)),
            $result,
        );
        self::assertStringContainsString(
            $this->normalizeContent(sprintf('Class Tests\\Kernel\\Utils\\Assert\\Doc\\Demo3 was generate succeed at %s/demo3.md.', $outputDirNameZhCn)),
            $result,
        );
        self::assertStringContainsString(
            $this->normalizeContent(sprintf('Class Tests\\Kernel\\Utils\\Assert\\Doc\\Demo2 was generate succeed at %s/demo2.md.', $outputDirNameZhCn)),
            $result,
        );
        self::assertStringContainsString(
            $this->normalizeContent(sprintf('Class Tests\\Kernel\\Utils\\Assert\\Doc\\Demo1 was generate succeed at %s/demo1.md.', $outputDirNameEnUs)),
            $result,
        );
        self::assertStringContainsString(
            $this->normalizeContent(sprintf('Class Tests\\Kernel\\Utils\\Assert\\Doc\\Demo3 was generate succeed at %s/demo3.md.', $outputDirNameEnUs)),
            $result,
        );
        self::assertStringContainsString(
            $this->normalizeContent(sprintf('Class Tests\\Kernel\\Utils\\Assert\\Doc\\Demo2 was generate succeed at %s/demo2.md.', $outputDirNameEnUs)),
            $result,
        );
        self::assertStringContainsString(
            $this->normalizeContent('A total of 4 files generate succeed.'),
            $result,
        );

        self::assertFileExists($outputDirNameDefault.'/demo1.md');
        self::assertFileExists($outputDirNameDefault.'/demo2.md');
        self::assertFileExists($outputDirNameDefault.'/demo3.md');
        self::assertFileExists($outputDirNameZhCn.'/demo1.md');
        self::assertFileExists($outputDirNameZhCn.'/demo2.md');
        self::assertFileExists($outputDirNameZhCn.'/demo3.md');
        self::assertFileExists($outputDirNameEnUs.'/demo1.md');
        self::assertFileExists($outputDirNameEnUs.'/demo2.md');
        self::assertFileExists($outputDirNameEnUs.'/demo3.md');

        Helper::deleteDirectory($outputDirNameDefault);
    }

    protected function initContainerService(IContainer $container): void
    {
        $app = new AppForDocFramework($container, '');
        $this->assertInstanceof(IApp::class, $app);
        $container->singleton(IApp::class, $app);
        $this->createConfig($container);
    }

    protected function createConfig(IContainer $container): void
    {
        $config = $this->createMock(IConfig::class);
        $map = [
            ['console\\framework_doc_outputdir', null, \dirname(__DIR__).'/Utils/Assert/Doc/Output{i18n}'],
            ['console\\framework_doc_git', null, 'https://github.com/hunzhiwange/framework/blob/master'],
            ['console\\framework_doc_logdir', null, ''],
            ['console\\framework_doc_i18n', null, ',zh-CN,en-US'],
        ];
        $config->method('get')->willReturnMap($map);
        $container->singleton(IConfig::class, static function () use ($config) {
            return $config;
        });
    }
}

class AppForDocFramework extends Apps
{
    public function path(string $path = ''): string
    {
        return 'notfoundbootstrap';
    }

    protected function registerBaseProvider(): void {}
}
