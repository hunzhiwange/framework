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
 * 字段过滤.
 *
 * @param mixed $fields
 *
 * @return mixed
 */
function fields_filter($fields)
{
    if (!is_array($fields)) {
        $fields = explode(',', $fields);
    }

    $fields = array_map(function ($str) {
        return sql_filter($str);
    }, $fields);

    $fields = implode(',', $fields);
    $fields = preg_replace('/^,|,$/', '', $fields);

    return $fields;
}

class fields_filter
{
}

fns(sql_filter::class);
