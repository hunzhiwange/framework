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

namespace Leevel\Encryption\Safe;

use RuntimeException;

/**
 * 检测代理.
 *
 * @throws \RuntimeException
 */
function limit_agent(): void
{
    if (!isset($_SERVER['HTTP_X_FORWARDED_FOR']) &&
        !isset($_SERVER['HTTP_VIA']) &&
        !isset($_SERVER['HTTP_PROXY_CONNECTION']) &&
        !isset($_SERVER['HTTP_USER_AGENT_VIA'])) {
        return;
    }

    $e = 'Proxy Connection denied.Your request was forbidden due to the '.
        'administrator has set to deny all proxy connection.';

    throw new RuntimeException($e);
}

class limit_agent
{
}
