<?php

declare(strict_types=1);

namespace Leevel\Support\Type;

/**
 * 验证数组中的每一项类型是否正确.
 *
 * - 数组支持 int,string 格式，值类型
 * - 数组支持 int:string,string:array 格式，键类型:值类型
 * - 数组支持 string:array:string:array:string:int 无限层级格式，键类型:值类型:键类型:值类型...(值类型|键类型:值类型)
 */
function arr(array $data, array $types): bool
{
    foreach ($data as $key => $value) {
        $result = false;
        foreach ($types as $type) {
            if (false !== ($position = strpos($type, ':'))) {
                if (type($key, substr($type, 0, $position)) &&
                    type($value, substr($type, $position + 1))) {
                    $result = true;

                    break;
                }
            } else {
                if (type($value, $type)) {
                    $result = true;

                    break;
                }
            }
        }

        if (false === $result) {
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
