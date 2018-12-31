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

use Exception;
use Leevel\Http\IRequest;
use Leevel\Http\IResponse;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * 异常接口.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.04.25
 *
 * @version 1.0
 */
interface IRuntime
{
    /**
     * 异常上报.
     *
     * @param \Exception $e
     *
     * @return mixed
     */
    public function report(Exception $e);

    /**
     * 异常渲染.
     *
     * @param \Leevel\Http\IRequest $request
     * @param \Exception            $e
     *
     * @return \Leevel\Http\IResponse
     */
    public function render(IRequest $request, Exception $e): IResponse;

    /**
     * 命令行渲染.
     *
     * @param \sSymfony\Component\Console\Output\OutputInterface $output
     * @param \Exception                                         $e
     */
    public function renderForConsole(OutputInterface $output, Exception $e);
}
