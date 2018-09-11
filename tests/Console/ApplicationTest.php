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
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Console;

use Leevel\Console\Application;
use Leevel\Console\Command;
use Leevel\Console\IApplication;
use Leevel\Di\Container;
use Tests\TestCase;

/**
 * application test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.06.04
 *
 * @version 1.0
 */
class ApplicationTest extends TestCase
{
    public function testBaseUse()
    {
        $application = new Application(new Container(), '1.0');

        $this->assertInstanceof(IApplication::class, $application);

        $this->assertSame($application->getVersion(), '1.0');

        $this->assertInstanceof(Container::class, $application->getContainer());

        $application->add(new Test1());

        $this->assertInstanceof(Test1::class, $application->get('test1'));
    }

    public function testNormalizeCommand()
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
    protected $name = 'test1';

    protected $description = 'test1 for command';

    public function handle()
    {
    }
}

class Test2 extends Command
{
    protected $name = 'test2';

    protected $description = 'test2 for command';

    public function __construct()
    {
        parent::__construct();

        $_SERVER['test'] = '1';
    }

    public function handle()
    {
    }
}
