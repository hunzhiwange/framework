<?php

declare(strict_types=1);

namespace Leevel\Support\Arr;

use InvalidArgumentException;
use JsonSerializable;
use Leevel\Support\IArray;
use Leevel\Support\IJson;

/**
 * 转换 JSON 数据.
 */
class ConvertJson
{
    /**
     * @throws \InvalidArgumentException
     */
    public static function handle(mixed $data = [], ?int $encodingOptions = null): string
    {
        if (null === $encodingOptions) {
            $encodingOptions = JSON_UNESCAPED_UNICODE;
        }

        if ($data instanceof IArray) {
            $data = json_encode($data->toArray(), $encodingOptions);
        } elseif ($data instanceof IJson) {
            $data = $data->toJson($encodingOptions);
        } elseif ($data instanceof JsonSerializable) {
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
}
