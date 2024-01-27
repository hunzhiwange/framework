<?php

declare(strict_types=1);

namespace Leevel\Support\Arr;

use Leevel\Support\IArray;
use Leevel\Support\IJson;

class ConvertJson
{
    /**
     * 转换 JSON 数据.
     *
     * @throws \InvalidArgumentException
     */
    public static function handle(mixed $data = [], ?int $encodingConfigs = null): string
    {
        if (null === $encodingConfigs) {
            $encodingConfigs = JSON_UNESCAPED_UNICODE;
        }

        if ($data instanceof IArray) {
            $data = json_encode($data->toArray(), $encodingConfigs);
        } elseif ($data instanceof IJson) {
            $data = $data->toJson($encodingConfigs);
        } elseif ($data instanceof \JsonSerializable) {
            $data = json_encode($data->jsonSerialize(), $encodingConfigs);
        } else {
            $data = json_encode($data, $encodingConfigs);
        }

        if (JSON_THROW_ON_ERROR & $encodingConfigs) {
            return (string) $data;
        }

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new \InvalidArgumentException(json_last_error_msg());
        }

        return (string) $data;
    }
}
