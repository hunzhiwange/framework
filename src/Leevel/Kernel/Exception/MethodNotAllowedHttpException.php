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

namespace Leevel\Kernel\Exception;

use Exception;

/**
 * 方法禁用.
 *
 * - 禁用请求中指定的方法: 405.
 */
abstract class MethodNotAllowedHttpException extends HttpException
{
    /**
     * 构造函数.
     */
    public function __construct(?string $message = null, int $code = 0, ?Exception $previous = null)
    {
        parent::__construct(405, $message, $code, $previous);
    }
}
