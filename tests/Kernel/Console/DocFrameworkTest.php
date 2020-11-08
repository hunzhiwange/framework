<?php

declare(strict_types=1);

/*
 * This file is part of the ************************ package.
 * _____________                           _______________
 *  ______/     \__  _____  ____  ______  / /_  _________
 *   ____/ __   / / / / _ \/ __`\/ / __ \/ __ \/ __ \___
 *    __/ / /  / /_/ /  __/ /  \  / /_/ / / / / /_/ /__
 *      \_\ \_/\____/\___/_/   / / .___/_/ /_/ .___/
 *         \_\                /_/_/         /_/
 *
 * The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
 * (c) 2010-2020 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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

class DocFrameworkTest extends TestCase
{
    use BaseCommand;

    public function testBaseUse(): void
    {
        $result = '';
        $dirName = dirname(__DIR__).'/Utils/Assert/Doc';
        $outputDirName = dirname(__DIR__).'/Utils/Assert/Doc/Output{i18n}';
        $this->obGetContents(function () use (&$result, $dirName, $outputDirName) {
            $result = $this->runCommand(
                new DocFramework(),
                [
                    'command' => 'make:docwithin',
                    'path'    => $dirName,
                ],
                function ($container) {
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

        $this->assertStringContainsString(
            $this->normalizeContent(sprintf('Class Tests\\Kernel\\Utils\\Assert\\Doc\\Demo1 was generate succeed at %s/demo1.md.', $outputDirNameDefault)),
            $result,
        );
        $this->assertStringContainsString(
            $this->normalizeContent(sprintf('Class Tests\\Kernel\\Utils\\Assert\\Doc\\Demo3 was generate succeed at %s/demo3.md.', $outputDirNameDefault)),
            $result,
        );
        $this->assertStringContainsString(
            $this->normalizeContent(sprintf('Class Tests\\Kernel\\Utils\\Assert\\Doc\\Demo2 was generate succeed at %s/demo2.md.', $outputDirNameDefault)),
            $result,
        );
        $this->assertStringContainsString(
            $this->normalizeContent(sprintf('Class Tests\\Kernel\\Utils\\Assert\\Doc\\Demo1 was generate succeed at %s/demo1.md.', $outputDirNameZhCn)),
            $result,
        );
        $this->assertStringContainsString(
            $this->normalizeContent(sprintf('Class Tests\\Kernel\\Utils\\Assert\\Doc\\Demo3 was generate succeed at %s/demo3.md.', $outputDirNameZhCn)),
            $result,
        );
        $this->assertStringContainsString(
            $this->normalizeContent(sprintf('Class Tests\\Kernel\\Utils\\Assert\\Doc\\Demo2 was generate succeed at %s/demo2.md.', $outputDirNameZhCn)),
            $result,
        );
        $this->assertStringContainsString(
            $this->normalizeContent(sprintf('Class Tests\\Kernel\\Utils\\Assert\\Doc\\Demo1 was generate succeed at %s/demo1.md.', $outputDirNameEnUs)),
            $result,
        );
        $this->assertStringContainsString(
            $this->normalizeContent(sprintf('Class Tests\\Kernel\\Utils\\Assert\\Doc\\Demo3 was generate succeed at %s/demo3.md.', $outputDirNameEnUs)),
            $result,
        );
        $this->assertStringContainsString(
            $this->normalizeContent(sprintf('Class Tests\\Kernel\\Utils\\Assert\\Doc\\Demo2 was generate succeed at %s/demo2.md.', $outputDirNameEnUs)),
            $result,
        );
        $this->assertStringContainsString(
            $this->normalizeContent('A total of 3 files generate succeed.'),
            $result,
        );

        $this->assertFileExists($outputDirNameDefault.'/demo1.md');
        $this->assertFileExists($outputDirNameDefault.'/demo2.md');
        $this->assertFileExists($outputDirNameDefault.'/demo3.md');
        $this->assertFileExists($outputDirNameZhCn.'/demo1.md');
        $this->assertFileExists($outputDirNameZhCn.'/demo2.md');
        $this->assertFileExists($outputDirNameZhCn.'/demo3.md');
        $this->assertFileExists($outputDirNameEnUs.'/demo1.md');
        $this->assertFileExists($outputDirNameEnUs.'/demo2.md');
        $this->assertFileExists($outputDirNameEnUs.'/demo3.md');

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
            ['console\\framework_doc_outputdir', null, dirname(__DIR__).'/Utils/Assert/Doc/Output{i18n}'],
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
