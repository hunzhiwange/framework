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

/**
 * 签名算法.
 *
 * @param array  $query
 * @param string $secret
 * @param array  $ignore
 *
 * @return string
 */
function signature(array $query, string $secret, array $ignore = []): string
{
    foreach ($ignore as $v) {
        if (isset($query[$v])) {
            unset($query[$v]);
        }
    }

    ksort($query);

    $sign = '';

    foreach ($query as $k => $v) {
        if (!is_array($v)) {
            $sign .= $k.'='.$v;
        } else {
            $sign .= $k.signature($v, $secret, $ignore);
        }
    }

    return hash_hmac('sha256', $sign, $secret);
}

class signature
{
}
