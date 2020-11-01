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
use Leevel\Kernel\App as Apps;
use Leevel\Kernel\Console\IdeHelper;
use Leevel\Kernel\IApp;
use Tests\Console\BaseCommand;
use Tests\Kernel\Utils\Assert\DemoClass;
use Tests\TestCase;

class IdeHelperTest extends TestCase
{
    use BaseCommand;

    public function testBaseUse(): void
    {
        $result = '';
        $outResult = $this->obGetContents(function () use (&$result) {
            $result = $this->runCommand(
                new IdeHelper(),
                [
                    'command' => 'make:idehelper',
                    'path'    => DemoClass::class,
                ],
                function ($container) {
                    $this->initContainerService($container);
                }
            );
        });

        $result = $this->normalizeContent($result);
        $outResult = $this->normalizeContent($outResult);

        $this->assertStringContainsString(
            $this->normalizeContent(sprintf('Ide helper for class %s generate succeed.', DemoClass::class)),
            $result,
        );
        $this->assertStringContainsString(
            $this->normalizeContent('* @method static void Demo1()'),
            $outResult,
        );
        $this->assertStringContainsString(
            $this->normalizeContent('* @method static void Demo2(string $hello, int $world)'),
            $outResult,
        );
        $this->assertStringContainsString(
            $this->normalizeContent('* @method static string Demo3(string $hello, ?int $world = null) demo3'),
            $outResult,
        );
        $this->assertStringContainsString(
            $this->normalizeContent('* @method static void Demo4(...$hello)'),
            $outResult,
        );
    }

    protected function initContainerService(IContainer $container): void
    {
        // 注册 app
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
