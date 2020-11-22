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

namespace Leevel\Kernel;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * 命令行内核执行接口.
 */
interface IKernelConsole
{
    /**
     * 响应命令行请求.
     */
    public function handle(?InputInterface $input = null, ?OutputInterface $output = null): int;

    /**
     * 执行结束.
     */
    public function terminate(int $status, ?InputInterface $input = null): void;

    /**
     * 初始化.
     */
    public function bootstrap(): void;

    /**
     * 返回应用.
     */
    public function getApp(): IApp;
}
