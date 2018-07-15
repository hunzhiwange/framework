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
use Leevel\Di\Container;
use Leevel\Support\Facade;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * base make.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.07.15
 *
 * @version 1.0
 */
trait BaseMake
{
    protected function runCommand(Command $command, array $inputs)
    {
        $container = new ContainerMock();

        $application = new Application($container, '1.0');

        $application->setAutoExit(false);

        Facade::setContainer($container);

        // 静态属性会保持住，可能受到其它单元测试的影响
        Facade::remove('router');

        $application->add($command);

        $container->singleton('router', function () {
            return new RouterService();
        });

        $input = new ArrayInput($inputs);
        $output = new BufferedOutput();

        $application->run($input, $output);

        $result = $output->fetch();

        Facade::setContainer(null);

        return $result;
    }
}

class RouterService
{
    public function getControllerDir()
    {
        return '';
    }
}

class ContainerMock extends Container
{
    public function getPathByNamespace($namespaces)
    {
        return __DIR__;
    }

    public function pathAnApplication(?string $app = null)
    {
        return __DIR__;
    }
}
