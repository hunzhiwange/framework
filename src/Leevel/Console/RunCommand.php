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

namespace Leevel\Console;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * 运行 command.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2019.07.04
 *
 * @version 1.0
 */
class RunCommand
{
    /**
     * 创建一个命令行应用程序.
     *
     * @var \Leevel\Console\IApplication
     */
    protected IApplication $application;

    /**
     * 创建一个命令行运行器.
     *
     * @param \Leevel\Console\IApplication $application
     *
     * @return \Leevel\Console\RunCommand
     */
    public function __construct(IApplication $application)
    {
        $this->application = $application;
        $application->setAutoExit(false);
    }

    /**
     * 运行一个命令.
     *
     * @param \Leevel\Console\Command|string $command
     * @param array                          $inputs
     *
     * @return string
     */
    public function handle($command, array $inputs): string
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
     *
     * @param \Leevel\Console\Command|string $command
     *
     * @return \Symfony\Component\Console\Command\Command
     */
    public function normalizeCommand($command): SymfonyCommand
    {
        if (is_string($command)) {
            return $this->application->normalizeCommand($command);
        }

        return $this->application->add($command);
    }
}
