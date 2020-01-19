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

use Exception;
use Leevel\Http\Request;
use Leevel\Http\Response;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * 异常接口.
 */
interface IExceptionRuntime
{
    /**
     * 异常上报.
     *
     * @return mixed
     */
    public function report(Exception $e);

    /**
     * 异常渲染.
     */
    public function render(Request $request, Exception $e): Response;

    /**
     * 命令行渲染.
     */
    public function renderForConsole(OutputInterface $output, Exception $e): void;
}
