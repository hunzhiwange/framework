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

use Leevel\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * 内核执行接口.
 */
interface IKernel
{
    /**
     * 响应 HTTP 请求.
     */
    public function handle(Request $request): Response;

    /**
     * 执行结束.
     */
    public function terminate(Request $request, Response $response): void;

    /**
     * 初始化.
     */
    public function bootstrap(): void;

    /**
     * 返回应用.
     */
    public function getApp(): IApp;
}
