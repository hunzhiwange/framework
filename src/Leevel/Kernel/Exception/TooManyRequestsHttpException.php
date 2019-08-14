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

namespace Leevel\Kernel\Exception;

use Exception;

/**
 * 请求过于频繁异常.
 *
 * - 用户在给定的时间内发送了太多的请求: 429.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.08.10
 *
 * @version 1.0
 */
class TooManyRequestsHttpException extends HttpException
{
    /**
     * 构造函数.
     *
     * @param null|string     $message
     * @param int             $code
     * @param null|\Exception $previous
     */
    public function __construct(?string $message = null, int $code = 0, ?Exception $previous = null)
    {
        parent::__construct(429, $message, $code, $previous);
    }
}
