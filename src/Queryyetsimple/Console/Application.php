<?php declare(strict_types=1);
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
namespace Leevel\Console;

use Leevel\Di\IContainer;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Application as SymfonyApplication;

/**
 * 命令行应用程序
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.04.28
 * @version 1.0
 */
class Application extends SymfonyApplication implements IApplication
{

    /**
     * 项目容器
     *
     * @var \Leevel\Di\IContainer
     */
    protected $container;

    /**
     * 创建一个命令行应用程序
     *
     * @param \Leevel\Di\IContainer $container
     * @param string $version
     * @return $this
     */
    public function __construct(IContainer $container, string $version)
    {
        $this->container = $container;

        parent::__construct($this->getLogo(), $version);
    }

    /**
     * 添加一条命令
     *
     * @param \Symfony\Component\Console\Command\Command $command
     * @return \Symfony\Component\Console\Command\Command
     */
    public function add(SymfonyCommand $command)
    {
        if ($command instanceof Command) {
            $command->setContainer($this->container);
        }

        return parent::add($command);
    }

    /**
     * 格式化一个命令行
     *
     * @param string $command
     * @return \Symfony\Component\Console\Command\Command
     */
    public function normalizeCommand(string $command)
    {
        return $this->add($this->container->make($command));
    }

    /**
     * 批量格式化命令行
     *
     * @param array $commands
     * @return $this
     */
    public function normalizeCommands(array $commands)
    {
        foreach ($commands as $command) {
            $this->normalizeCommand($command);
        }

        return $this;
    }

    /**
     * 返回项目容器
     *
     * @return \Leevel\Di\Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * 返回 QueryPHP Logo
     *
     * @return string
     */
    protected function getLogo()
    {
        return <<<queryphp
_____________                           _______________
 ______/     \__  _____  ____  ______  / /_  _________
  ____/ __   / / / / _ \/ __`\/ / __ \/ __ \/ __ \___
   __/ / /  / /_/ /  __/ /  \  / /_/ / / / / /_/ /__
     \_\ \_/\____/\___/_/   / / .___/_/ /_/ .___/
        \_\                /_/_/         /_/
queryphp;
    }
}
