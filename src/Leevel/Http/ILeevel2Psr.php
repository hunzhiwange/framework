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

namespace Leevel\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Leevel 规范请求转 Psr 接口
 * This class borrows heavily from the Symfony4 Framework and is part of the symfony package.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @author Kévin Dunglas <dunglas@gmail.com>
 *
 * @since 2019.03.11
 *
 * @version 1.0
 *
 * @see Symfony\Bridge\PsrHttpMessage (https://github.com/symfony/psr-http-message-bridge)
 */
interface ILeevel2Psr
{
    /**
     * 从 Leevel 请求对象创建 Psr 请求对象
     *
     * @param \Leevel\Http\IRequest $leevelRequest
     *
     * @return \Psr\Http\Message\ServerRequestInterface
     */
    public function createRequest(IRequest $leevelRequest): ServerRequestInterface;

    /**
     * 从 Leevel 响应对象创建 Psr 响应对象
     *
     * @param \Leevel\Http\IResponse $leevelResponse
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function createResponse(Response $leevelResponse): ResponseInterface;
}
