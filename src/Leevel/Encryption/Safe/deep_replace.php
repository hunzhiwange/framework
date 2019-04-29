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
 * 深度过滤.
 *
 * @param array  $search
 * @param string $subject
 *
 * @return string
 */
function deep_replace(array $search, string $subject): string
{
    $found = true;

    while ($found) {
        $found = false;

        foreach ($search as $val) {
            while (false !== strpos($subject, $val)) {
                $found = true;
                $subject = str_replace($val, '', $subject);
            }
        }
    }

    return $subject;
}

class deep_replace
{
}
