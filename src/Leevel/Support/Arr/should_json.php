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

namespace Leevel\Support\Arr;

use ArrayObject;
use JsonSerializable;
use Leevel\Support\IArray;
use Leevel\Support\IJson;

/**
 * 可以转换为 JSON.
 *
 * @param mixed $content
 */
function should_json($content): bool
{
    return $content instanceof IJson ||
        $content instanceof IArray ||
        $content instanceof ArrayObject ||
        $content instanceof JsonSerializable ||
        is_array($content);
}

class should_json
{
}