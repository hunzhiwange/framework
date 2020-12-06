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

class DocTest extends TestCase
{
    use BaseCommand;

    public function testBaseUse(): void
    {
        $result = '';
        $dirName = dirname(__DIR__).'/Utils/Assert/Doc';
        $outputDirName = dirname(__DIR__).'/Utils/Assert/Doc/Output{i18n}';
        $this->obGetContents(function () use (&$result, $dirName, $outputDirName) {
            $result = $this->runCommand(
                new Doc(),
                [
                    'command'   => 'make:doc',
                    'path'      => $dirName,
                    'bootstrap' => '',
                    'outputdir' => $outputDirName,
                    'git'       => 'https://github.com/hunzhiwange/framework/blob/master',
                    '--i18n'    => 'zh-CN',
                ],
                function ($container) {
                    $this->initContainerService($container);
                }
            );
        });

        $result = $this->normalizeContent($result);
        $outputDirName = str_replace('{i18n}', '/zh-CN', $outputDirName);

        $this->assertStringContainsString(
            $this->normalizeContent(sprintf('Class Tests\\Kernel\\Utils\\Assert\\Doc\\Demo1 was generate succeed at %s/demo1.md.', $outputDirName)),
            $result,
        );
        $this->assertStringContainsString(
            $this->normalizeContent(sprintf('Class Tests\\Kernel\\Utils\\Assert\\Doc\\Demo3 was generate succeed at %s/demo3.md.', $outputDirName)),
            $result,
        );
        $this->assertStringContainsString(
            $this->normalizeContent(sprintf('Class Tests\\Kernel\\Utils\\Assert\\Doc\\Demo2 was generate succeed at %s/demo2.md.', $outputDirName)),
            $result,
        );
        $this->assertStringContainsString(
            $this->normalizeContent('A total of 3 files generate succeed.'),
            $result,
        );

        $this->assertFileExists($outputDirName.'/demo1.md');
        $this->assertFileExists($outputDirName.'/demo2.md');
        $this->assertFileExists($outputDirName.'/demo3.md');

        Helper::deleteDirectory(dirname($outputDirName));
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
