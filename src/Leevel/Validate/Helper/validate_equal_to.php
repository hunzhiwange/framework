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

namespace Leevel\Validate\Helper;

use InvalidArgumentException;

/**
 * 两个字段是否相同.
 *
 * @param mixed $datas
 * @param array $parameter
 * @param array $meta
 *
 * @return bool
 */
function validate_equal_to($datas, array $parameter, array $meta = []): bool
{
    if (1 > count($parameter)) {
        throw new InvalidArgumentException('At least 1 parameter.');
    }

    return $datas === get_field_value($meta, $parameter[0]);
}

// @codeCoverageIgnoreStart
if (!function_exists('Leevel\\Validate\\Helper\\get_field_value')) {
    include __DIR__.'/get_field_value.php';
}
// @codeCoverageIgnoreEnd
