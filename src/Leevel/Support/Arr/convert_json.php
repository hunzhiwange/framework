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

use InvalidArgumentException;
use JsonSerializable;
use Leevel\Support\IArray;
use Leevel\Support\IJson;

/**
 * 转换 JSON 数据.
 *
 * @throws \InvalidArgumentException
 */
function convert_json(mixed $data = [], ?int $encodingOptions = null): string
{
    if (null === $encodingOptions) {
        $encodingOptions = JSON_UNESCAPED_UNICODE;
    }

    if ($data instanceof IArray) {
        $data = json_encode($data->toArray(), $encodingOptions);
    } elseif (is_object($data) && $data instanceof IJson) {
        $data = $data->toJson($encodingOptions);
    } elseif (is_object($data) && $data instanceof JsonSerializable) {
        $data = json_encode($data->jsonSerialize(), $encodingOptions);
    } else {
        $data = json_encode($data, $encodingOptions);
    }

    if (JSON_THROW_ON_ERROR & $encodingOptions) {
        return (string) $data;
    }

    if (JSON_ERROR_NONE !== json_last_error()) {
        throw new InvalidArgumentException(json_last_error_msg());
    }

    return (string) $data;
}

class convert_json
{
}
