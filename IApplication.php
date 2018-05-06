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

use Symfony\Component\Console\Command\Command as SymfonyCommand;

/**
 * 命令行应用程序接口
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.04.28
 * @version 1.0
 */
interface IApplication
{

    /**
     * 添加一条命令
     *
     * @param \Symfony\Component\Console\Command\Command $command
     * @return \Symfony\Component\Console\Command\Command
     */
    public function add(SymfonyCommand $command);

    /**
     * 格式化一个命令行
     *
     * @param string $command
     * @return \Symfony\Component\Console\Command\Command
     */
    public function normalizeCommand(string $command);

    /**
     * 批量格式化命令行
     *
     * @param array $commands
     * @return $this
     */
    public function normalizeCommands(array $commands);

    /**
     * 返回项目容器
     *
     * @return \Leevel\Di\Container
     */
    public function getContainer();
}
