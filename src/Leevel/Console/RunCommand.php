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

namespace Leevel\Console;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * 运行 command.
 */
class RunCommand
{
    /**
     * 创建一个命令行应用程序.
     *
     * @var \Leevel\Console\Application
     */
    protected Application $application;

    /**
     * 创建一个命令行运行器.
     */
    public function __construct(Application $application)
    {
        $this->application = $application;
        $application->setAutoExit(false);
    }

    /**
     * 运行一个命令.
     */
    public function handle(string|Command $command, array $inputs): string
    {
        $this->normalizeCommand($command);
        $input = new ArrayInput($inputs);
        $output = new BufferedOutput();
        $this->application->run($input, $output);
        $result = $output->fetch();

        return $result;
    }

    /**
     * 格式化一个命令行.
     */
    public function normalizeCommand(string|Command $command): ?SymfonyCommand
    {
        if (is_string($command)) {
            return $this->application->normalizeCommand($command);
        }

        return $this->application->add($command);
    }
}
