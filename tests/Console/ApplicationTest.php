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

namespace Tests\Console;

use Leevel\Console\Application;
use Leevel\Console\Command;
use Leevel\Di\Container;
use Tests\TestCase;

class ApplicationTest extends TestCase
{
    public function testBaseUse(): void
    {
        $application = new Application(new Container(), '1.0');

        $this->assertSame($application->getVersion(), '1.0');

        $this->assertInstanceof(Container::class, $application->getContainer());

        $application->add(new Test1());

        $this->assertInstanceof(Test1::class, $application->get('test1'));
    }

    public function testNormalizeCommand(): void
    {
        $application = new Application(new Container(), '1.0');

        if (isset($_SERVER['test'])) {
            unset($_SERVER['test']);
        }

        $application->normalizeCommands([Test2::class]);

        $this->assertSame($_SERVER['test'], '1');

        unset($_SERVER['test']);
    }
}

class Test1 extends Command
{
    protected string $name = 'test1';

    protected string $description = 'test1 for command';

    public function handle()
    {
    }
}

class Test2 extends Command
{
    protected string $name = 'test2';

    protected string $description = 'test2 for command';

    public function __construct()
    {
        parent::__construct();

        $_SERVER['test'] = '1';
    }

    public function handle()
    {
    }
}
