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
namespace Leevel\Bootstrap;

use Leevel\Http\Request;
use Leevel\Http\IResponse;

/**
 * 内核执行接口
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2016.11.18
 * @version 1.0
 */
interface IKernel
{

    /**
     * 响应 HTTP 请求
     *
     * @param \Leevel\Http\Request $request
     * @return \Leevel\Http\IResponse
     */
    public function handle(Request $request);

    /**
     * 执行结束
     *
     * @param \Leevel\Http\Request $request
     * @param \Leevel\Http\IResponse $response
     * @return void
     */
    public function terminate(Request $request, IResponse $response);

    /**
     * 返回项目
     *
     * @return \Leevel\Bootstrap\IProject
     */
    public function getProject();
}
