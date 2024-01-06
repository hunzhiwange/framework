<?php

declare(strict_types=1);

namespace Tests\Kernel\Console;

use Leevel\Di\IContainer;
use Leevel\Filesystem\Helper;
use Leevel\Kernel\App as Apps;
use Leevel\Kernel\Console\Doc;
use Leevel\Kernel\Console\DocFramework;
use Leevel\Kernel\IApp;
use Leevel\Option\IOption;
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

        static::assertStringContainsString(
            $this->normalizeContent(sprintf('Class Tests\\Kernel\\Utils\\Assert\\Doc\\Demo1 was generate succeed at %s/demo1.md.', $outputDirNameDefault)),
            $result,
        );
        static::assertStringContainsString(
            $this->normalizeContent(sprintf('Class Tests\\Kernel\\Utils\\Assert\\Doc\\Demo3 was generate succeed at %s/demo3.md.', $outputDirNameDefault)),
            $result,
        );
        static::assertStringContainsString(
            $this->normalizeContent(sprintf('Class Tests\\Kernel\\Utils\\Assert\\Doc\\Demo2 was generate succeed at %s/demo2.md.', $outputDirNameDefault)),
            $result,
        );
        static::assertStringContainsString(
            $this->normalizeContent(sprintf('Class Tests\\Kernel\\Utils\\Assert\\Doc\\Demo1 was generate succeed at %s/demo1.md.', $outputDirNameZhCn)),
            $result,
        );
        static::assertStringContainsString(
            $this->normalizeContent(sprintf('Class Tests\\Kernel\\Utils\\Assert\\Doc\\Demo3 was generate succeed at %s/demo3.md.', $outputDirNameZhCn)),
            $result,
        );
        static::assertStringContainsString(
            $this->normalizeContent(sprintf('Class Tests\\Kernel\\Utils\\Assert\\Doc\\Demo2 was generate succeed at %s/demo2.md.', $outputDirNameZhCn)),
            $result,
        );
        static::assertStringContainsString(
            $this->normalizeContent(sprintf('Class Tests\\Kernel\\Utils\\Assert\\Doc\\Demo1 was generate succeed at %s/demo1.md.', $outputDirNameEnUs)),
            $result,
        );
        static::assertStringContainsString(
            $this->normalizeContent(sprintf('Class Tests\\Kernel\\Utils\\Assert\\Doc\\Demo3 was generate succeed at %s/demo3.md.', $outputDirNameEnUs)),
            $result,
        );
        static::assertStringContainsString(
            $this->normalizeContent(sprintf('Class Tests\\Kernel\\Utils\\Assert\\Doc\\Demo2 was generate succeed at %s/demo2.md.', $outputDirNameEnUs)),
            $result,
        );
        static::assertStringContainsString(
            $this->normalizeContent('A total of 4 files generate succeed.'),
            $result,
        );

        static::assertFileExists($outputDirNameDefault.'/demo1.md');
        static::assertFileExists($outputDirNameDefault.'/demo2.md');
        static::assertFileExists($outputDirNameDefault.'/demo3.md');
        static::assertFileExists($outputDirNameZhCn.'/demo1.md');
        static::assertFileExists($outputDirNameZhCn.'/demo2.md');
        static::assertFileExists($outputDirNameZhCn.'/demo3.md');
        static::assertFileExists($outputDirNameEnUs.'/demo1.md');
        static::assertFileExists($outputDirNameEnUs.'/demo2.md');
        static::assertFileExists($outputDirNameEnUs.'/demo3.md');

        Helper::deleteDirectory($outputDirNameDefault);
    }

    protected function initContainerService(IContainer $container): void
    {
        $app = new AppForDocFramework($container, '');
        $this->assertInstanceof(IApp::class, $app);
        $container->singleton(IApp::class, $app);
        $this->createOption($container);
    }

    protected function createOption(IContainer $container): void
    {
        $option = $this->createMock(IOption::class);
        $map = [
            ['console\\framework_doc_outputdir', null, \dirname(__DIR__).'/Utils/Assert/Doc/Output{i18n}'],
            ['console\\framework_doc_git', null, 'https://github.com/hunzhiwange/framework/blob/master'],
            ['console\\framework_doc_logdir', null, ''],
            ['console\\framework_doc_i18n', null, ',zh-CN,en-US'],
        ];
        $option->method('get')->willReturnMap($map);
        $container->singleton(IOption::class, function () use ($option) {
            return $option;
        });
    }
}

class AppForDocFramework extends Apps
{
    public function path(string $path = ''): string
    {
        return 'notfoundbootstrap';
    }

    protected function registerBaseProvider(): void
    {
    }
}
