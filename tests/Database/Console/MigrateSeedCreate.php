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

namespace Tests\Database\Console;

use Leevel\Database\Console\SeedCreate;
use Leevel\Di\IContainer;
use Leevel\Kernel\IApp;
use Tests\Console\BaseCommand;
use Tests\TestCase;

class MigrateSeedCreate extends TestCase
{
    use BaseCommand;

    protected function setUp(): void
    {
        $this->tearDown();
    }

    protected function tearDown(): void
    {
        $seedsFile = dirname(__DIR__, 2).'/assert/database/seeds/HelloWorld.php';
        if (is_file($seedsFile)) {
            unlink($seedsFile);
        }
    }

    public function testBaseUse(): void
    {
        $result = $this->runCommand(
            new SeedCreate(),
            [
                'command' => 'migrate:seedcreate',
                'name'    => 'HelloWorld',
            ],
            function ($container) {
                $this->initContainerService($container);
            }
        );

        $result = $this->normalizeContent($result);

        $this->assertStringContainsString(
            $this->normalizeContent('using config file ./phinx.php'),
            $result
        );

        $this->assertStringContainsString(
            $this->normalizeContent('using config parser php'),
            $result
        );

        $this->assertStringContainsString(
            $this->normalizeContent('using seed base class Phinx\\Seed\\AbstractSeed'),
            $result
        );

        $this->assertStringContainsString(
            $this->normalizeContent('created ./tests/assert/database/seeds/HelloWorld.php'),
            $result
        );
    }

    protected function initContainerService(IContainer $container): void
    {
        $app = $this->createMock(IApp::class);
        $this->assertInstanceof(IApp::class, $app);

        $container->singleton(IApp::class, $app);
    }
}
