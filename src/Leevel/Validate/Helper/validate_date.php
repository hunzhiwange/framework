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

use DateTime;

/**
 * 是否为日期
 *
 * @param mixed $datas
 *
 * @return bool
 */
function validate_date($datas): bool
{
    if ($datas instanceof DateTime) {
        return true;
    }

    if (!is_scalar($datas)) {
        return false;
    }

    if (false === strtotime((string) ($datas))) {
        return false;
    }

    $datas = date_parse($datas);

    if (false === $datas['year'] ||
        false === $datas['month'] ||
        false === $datas['day']) {
        return false;
    }

    return checkdate($datas['month'], $datas['day'], $datas['year']);
}
