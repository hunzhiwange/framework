<?php

declare(strict_types=1);

namespace Leevel\Support\Type;

class Type
{
    /**
     * 验证 PHP 各种变量类型.
     *
     * - 支持 PHP 内置或者自定义的 is_array,is_int,is_custom 等函数
     * - 数组支持 array:int,string 格式，值类型
     * - 数组支持 array:int:string,string:array 格式，键类型:值类型
     * - 数组支持 array:string:array:string:array:string:int 无限层级格式，键类型:值类型:键类型:值类型...(值类型|键类型:值类型)
     *
     * @see https://www.php.net/manual/zh/function.is-array.php
     */
    public static function handle(mixed $value, string $type): bool
    {
        if (\is_array($value) && str_starts_with($type, 'array:')) {
            return Arr::handle($value, explode(',', substr($type, 6)));
        }

        if (\function_exists($isTypeFunction = 'is_'.$type)) {
            return $isTypeFunction($value);
        }

        return $value instanceof $type;
    }
}
