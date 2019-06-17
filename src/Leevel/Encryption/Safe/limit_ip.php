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
 * IP 访问限制.
 *
 * @param string $visitorIp
 * @param array  $limitIp
 *
 * @throws \RuntimeException
 */
function limit_ip(string $visitorIp, array $limitIp): void
{
    if (empty($limitIp)) {
        return;
    }

    foreach ($limitIp as $ip) {
        if (!preg_match("/{$ip}/", $visitorIp)) {
            continue;
        }

        $e = sprintf('You IP %s are banned,you cannot access this.', $visitorIp);

        throw new RuntimeException($e);
    }
}

class limit_ip
{
}
