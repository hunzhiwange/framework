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

namespace Leevel\Encryption\Safe;

/**
 * 字符 HTML 实体还原.
 *
 * @param mixed $data
 *
 * @return mixed
 */
function un_htmlspecialchars($data)
{
    if (!is_array($data)) {
        $data = (array) $data;
    }

    $data = array_map(function ($data) {
        $data = strtr(
            $data,
            array_flip(
                get_html_translation_table(HTML_SPECIALCHARS)
            )
        );

        return $data;
    }, $data);

    if (1 === count($data)) {
        $data = reset($data);
    }

    return $data;
}

class un_htmlspecialchars
{
}
