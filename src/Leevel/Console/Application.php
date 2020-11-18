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

use Leevel\Di\IContainer;
use Symfony\Component\Console\Application as SymfonyApplication;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

/**
 * 命令行应用程序.
 */
class Application extends SymfonyApplication
{
    /**
     * IOC 容器.
     *
     * @var \Leevel\Di\IContainer
     */
    protected IContainer $container;

    /**
     * 创建一个命令行应用程序.
     */
    public function __construct(IContainer $container, string $version)
    {
        $this->container = $container;
        parent::__construct($this->getLogo(), $version);
    }

    /**
     * 添加一条命令.
     */
    public function add(SymfonyCommand $command): ?SymfonyCommand
    {
        if ($command instanceof Command) {
            $command->setContainer($this->container);
        }

        return parent::add($command);
    }

    /**
     * 格式化一个命令行.
     */
    public function normalizeCommand(string $command): ?SymfonyCommand
    {
        return $this->add($this->container->make($command));
    }

    /**
     * 批量格式化命令行.
     */
    public function normalizeCommands(array $commands): void
    {
        foreach ($commands as $command) {
            $this->normalizeCommand($command);
        }
    }

    /**
     * 返回应用容器.
     */
    public function getContainer(): IContainer
    {
        return $this->container;
    }

    /**
     * 返回 QueryPHP Logo.
     */
    protected function getLogo(): string
    {
        return <<<'queryphp'
            _____________                           _______________
             ______/     \__  _____  ____  ______  / /_  _________
              ____/ __   / / / / _ \/ __`\/ / __ \/ __ \/ __ \___
               __/ / /  / /_/ /  __/ /  \  / /_/ / / / / /_/ /__
                 \_\ \_/\____/\___/_/   / / .___/_/ /_/ .___/
                    \_\                /_/_/         /_/
                      by Xiangmin Liu and contributors.
            queryphp;
    }
}
