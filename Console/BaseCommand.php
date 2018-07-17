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

use Closure;
use Leevel\Console\Application;
use Leevel\Console\Command;
use Leevel\Di\Container;
use Leevel\Support\Facade;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * base command.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.07.17
 *
 * @version 1.0
 */
trait BaseCommand
{
    protected function runCommand(Command $command, array $inputs, Closure $call)
    {
        $container = new Container();

        $application = new Application($container, '1.0');

        $application->setAutoExit(false);

        Facade::setContainer($container);

        call_user_func($call, $container);

        $application->add($command);

        $input = new ArrayInput($inputs);
        $output = new BufferedOutput();

        $application->run($input, $output);

        $result = $output->fetch();

        Facade::setContainer(null);

        return $result;
    }
}
