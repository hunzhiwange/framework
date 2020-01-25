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

use Exception;
use InvalidArgumentException;
use JsonSerializable;
use Leevel\Support\IArray;
use Leevel\Support\IJson;

/**
 * 转换 JSON 数据.
 *
 * @param mixed $data
 *
 * @throws \InvalidArgumentException
 */
function convert_json($data = [], ?int $encodingOptions = null, bool $verifyIsJson = true): string
{
    if ($verifyIsJson && !should_json($data)) {
        return $data;
    }

    if (null === $encodingOptions) {
        $encodingOptions = JSON_UNESCAPED_UNICODE;
    }

    try {
        if ($data instanceof IArray) {
            $data = json_encode($data->toArray(), $encodingOptions);
        } elseif (is_object($data) && $data instanceof IJson) {
            $data = $data->toJson($encodingOptions);
        } elseif (is_object($data) && $data instanceof JsonSerializable) {
            $data = json_encode($data->jsonSerialize(), $encodingOptions);
        } else {
            $data = json_encode($data, $encodingOptions);
        }
    } catch (Exception $e) {
        if ('Exception' === get_class($e) &&
            0 === strpos($e->getMessage(), 'Failed calling ')) {
            throw $e->getPrevious() ?: $e;
        }

        throw $e;
    }

    if (JSON_THROW_ON_ERROR & $encodingOptions) {
        return $data;
    }

    if (JSON_ERROR_NONE !== json_last_error()) {
        throw new InvalidArgumentException(json_last_error_msg());
    }

    return (string) $data;
}

class convert_json
{
}

// import fn.
class_exists(should_json::class);
