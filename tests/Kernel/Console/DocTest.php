<?php

declare(strict_types=1);

namespace Tests\Kernel\Console;

use Leevel\Di\IContainer;
use Leevel\Filesystem\Helper;
use Leevel\Kernel\App as Apps;
use Leevel\Kernel\Console\Doc;
use Leevel\Kernel\IApp;
use Tests\Console\BaseCommand;
use Tests\TestCase;

final class DocTest extends TestCase
{
    use BaseCommand;

    protected function setUp(): void
    {
        $this->tearDown();
    }

    protected function tearDown(): void
    {
        $outputDirName = \dirname(__DIR__).'/Utils/Assert/Doc/Output';
        if (is_dir($outputDirName)) {
            Helper::deleteDirectory($outputDirName);
        }

        if (isset($GLOBALS['make:doc:bootstrap'])) {
            unset($GLOBALS['make:doc:bootstrap']);
        }
    }

    public function testBaseUse(): void
    {
        $result = '';
        $dirName = \dirname(__DIR__).'/Utils/Assert/Doc';
        $outputDirName = \dirname(__DIR__).'/Utils/Assert/Doc/Output{i18n}';
        $this->obGetContents(function () use (&$result, $dirName, $outputDirName): void {
            $result = $this->runCommand(
                new Doc(),
                [
                    'command' => 'make:doc',
                    'path' => $dirName,
                    'outputdir' => $outputDirName,
                    '--bootstrap' => '',
                    '--git' => 'https://github.com/hunzhiwange/framework/blob/master',
                    '--i18n' => 'zh-CN',
                ],
                function ($container): void {
                    $this->initContainerService($container);
                }
            );
        });

        $result = $this->normalizeContent($result);
        $outputDirName = str_replace('{i18n}', '/zh-CN', $outputDirName);

        static::assertStringContainsString(
            $this->normalizeContent(sprintf('Class Tests\\Kernel\\Utils\\Assert\\Doc\\Demo1 was generate succeed at %s/demo1.md.', $outputDirName)),
            $result,
        );
        static::assertStringContainsString(
            $this->normalizeContent(sprintf('Class Tests\\Kernel\\Utils\\Assert\\Doc\\Demo3 was generate succeed at %s/demo3.md.', $outputDirName)),
            $result,
        );
        static::assertStringContainsString(
            $this->normalizeContent(sprintf('Class Tests\\Kernel\\Utils\\Assert\\Doc\\Demo2 was generate succeed at %s/demo2.md.', $outputDirName)),
            $result,
        );
        static::assertStringContainsString(
            $this->normalizeContent('A total of 3 files generate succeed.'),
            $result,
        );

        static::assertFileExists($outputDirName.'/demo1.md');
        static::assertFileExists($outputDirName.'/demo2.md');
        static::assertFileExists($outputDirName.'/demo3.md');
    }

    public function test1(): void
    {
        $result = '';
        $dirName = \dirname(__DIR__).'/Utils/Assert/DocNot';
        $outputDirName = \dirname(__DIR__).'/Utils/Assert/Doc/Output{i18n}';
        $this->obGetContents(function () use (&$result, $dirName, $outputDirName): void {
            $result = $this->runCommand(
                new Doc(),
                [
                    'command' => 'make:doc',
                    'path' => $dirName,
                    'outputdir' => $outputDirName,
                    '--bootstrap' => '',
                    '--git' => 'https://github.com/hunzhiwange/framework/blob/master',
                    '--i18n' => 'zh-CN',
                ],
                function ($container): void {
                    $this->initContainerService($container);
                }
            );
        });

        $result = $this->normalizeContent($result);

        static::assertStringContainsString(
            $this->normalizeContent('Files was not found.'),
            $result,
        );
    }

    public function test2(): void
    {
        $result = '';
        $dirName = \dirname(__DIR__).'/Utils/Assert/Doc/Demo1.php';
        $outputDirName = \dirname(__DIR__).'/Utils/Assert/Doc/Output{i18n}';
        $this->obGetContents(function () use (&$result, $dirName, $outputDirName): void {
            $result = $this->runCommand(
                new Doc(),
                [
                    'command' => 'make:doc',
                    'path' => $dirName,
                    'outputdir' => $outputDirName,
                    '--bootstrap' => '',
                    '--git' => 'https://github.com/hunzhiwange/framework/blob/master',
                    '--i18n' => 'zh-CN',
                ],
                function ($container): void {
                    $this->initContainerService($container);
                }
            );
        });

        $result = $this->normalizeContent($result);
        $outputDirName = str_replace('{i18n}', '/zh-CN', $outputDirName);

        static::assertStringContainsString(
            $this->normalizeContent(sprintf('Class Tests\\Kernel\\Utils\\Assert\\Doc\\Demo1 was generate succeed at %s/demo1.md.', $outputDirName)),
            $result,
        );
        static::assertStringContainsString(
            $this->normalizeContent('A total of 1 files generate succeed.'),
            $result,
        );

        static::assertFileExists($outputDirName.'/demo1.md');
    }

    public function test3(): void
    {
        $result = '';
        $dirName = \dirname(__DIR__).'/Utils/Assert/Doc/Demo1.php';
        $outputDirName = \dirname(__DIR__).'/Utils/Assert/Doc/Output{i18n}';
        $logDirName = \dirname(__DIR__).'/Utils/Assert/Doc/Output/log';
        $this->obGetContents(function () use (&$result, $dirName, $outputDirName, $logDirName): void {
            $result = $this->runCommand(
                new Doc(),
                [
                    'command' => 'make:doc',
                    'path' => $dirName,
                    'outputdir' => $outputDirName,
                    '--bootstrap' => '',
                    '--git' => 'https://github.com/hunzhiwange/framework/blob/master',
                    '--i18n' => 'zh-CN',
                    '--logdir' => $logDirName,
                ],
                function ($container): void {
                    $this->initContainerService($container);
                }
            );
        });

        $result = $this->normalizeContent($result);
        $outputDirName = str_replace('{i18n}', '/zh-CN', $outputDirName);

        static::assertStringContainsString(
            $this->normalizeContent(sprintf('Class Tests\\Kernel\\Utils\\Assert\\Doc\\Demo1 was generate succeed at %s/demo1.md.', $outputDirName)),
            $result,
        );
        static::assertStringContainsString(
            $this->normalizeContent('A total of 1 files generate succeed.'),
            $result,
        );

        static::assertFileExists($outputDirName.'/demo1.md');
    }

    public function test4(): void
    {
        $result = '';
        $dirName = \dirname(__DIR__).'/Utils/Assert/Doc/Demo1.php';
        $outputDirName = \dirname(__DIR__).'/Utils/Assert/Doc/Output{i18n}';
        $logDirName = \dirname(__DIR__).'/Utils/Assert/Doc/Output/log';
        $bootstrapFile = \dirname(__DIR__).'/Utils/bootstrap.php';
        $this->obGetContents(function () use (&$result, $dirName, $outputDirName, $logDirName, $bootstrapFile): void {
            $result = $this->runCommand(
                new Doc(),
                [
                    'command' => 'make:doc',
                    'path' => $dirName,
                    'outputdir' => $outputDirName,
                    '--bootstrap' => $bootstrapFile,
                    '--git' => 'https://github.com/hunzhiwange/framework/blob/master',
                    '--i18n' => 'zh-CN',
                    '--logdir' => $logDirName,
                ],
                function ($container): void {
                    $this->initContainerService($container);
                }
            );
        });

        $result = $this->normalizeContent($result);
        $outputDirName = str_replace('{i18n}', '/zh-CN', $outputDirName);

        static::assertStringContainsString(
            $this->normalizeContent(sprintf('Class Tests\\Kernel\\Utils\\Assert\\Doc\\Demo1 was generate succeed at %s/demo1.md.', $outputDirName)),
            $result,
        );
        static::assertStringContainsString(
            $this->normalizeContent('A total of 1 files generate succeed.'),
            $result,
        );

        static::assertFileExists($outputDirName.'/demo1.md');
        static::assertSame(1, $GLOBALS['make:doc:bootstrap']);
    }

    protected function initContainerService(IContainer $container): void
    {
        $app = new AppForDoc($container, '');
        $this->assertInstanceof(IApp::class, $app);
        $container->singleton(IApp::class, $app);
    }
}

class AppForDoc extends Apps
{
    protected function registerBaseProvider(): void
    {
    }
}
