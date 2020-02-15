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

namespace Leevel\Support\Type;

/**
 * 验证数组中的每一项类型是否正确.
 */
function arr(array $data, array $types): bool
{
    foreach ($data as $value) {
        $result = false;
        foreach ($types as $type) {
            if (type($value, $type)) {
                $result = true;

                break;
            }
        }

        if (!$result) {
            return false;
        }
    }

    return true;
}

class arr
{
}

// import fn.
class_exists(type::class);
