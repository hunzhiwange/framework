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

namespace Leevel\Kernel;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * 命令行内核执行接口.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.05.04
 *
 * @version 1.0
 */
interface IKernelConsole
{
    /**
     * 响应命令行请求
     *
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    public function handle(InputInterface $input = null, OutputInterface $output = null): int;

    /**
     * 执行结束
     *
     * @param int                                             $status
     * @param \Symfony\Component\Console\Input\InputInterface $input
     */
    public function terminate(int $status, InputInterface $input = null): void;

    /**
     * 初始化.
     */
    public function bootstrap(): void;

    /**
     * 返回项目.
     *
     * @return \Leevel\Kernel\IProject
     */
    public function getProject(): IProject;
}
