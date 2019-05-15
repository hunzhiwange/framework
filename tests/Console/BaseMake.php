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
 * (c) 2010-2019 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Console;

use Closure;
use Composer\Autoload\ClassLoader;
use Leevel\Console\Application;
use Leevel\Console\Command;
use Leevel\Di\Container;
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
    protected function runCommand(Command $command, array $inputs, Closure $call = null)
    {
        $container = Container::singletons();
        $container->clear();
        $container->instance('composer', new ComposerMock());

        $application = new Application($container, '1.0');

        $application->setAutoExit(false);

        if ($call) {
            call_user_func($call, $container, $application);
        }

        $application->add($command);

        $container->singleton('router', function () {
            return new RouterService();
        });

        $input = new ArrayInput($inputs);
        $output = new BufferedOutput();

        $application->run($input, $output);

        $result = $output->fetch();

        $container->clear();

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

class ComposerMock extends ClassLoader
{
    public function findFile($class)
    {
        // mock for `\\Common\\index`
        return __DIR__.'/index.php';
    }
}
