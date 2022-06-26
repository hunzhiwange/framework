<?php

declare(strict_types=1);

namespace Leevel\Support;

/**
 * 类型判断辅助函数.
 *
 * @method static bool arr(array $data, array $types)                                  验证数组中的每一项类型是否正确.
 * @method static float|int|string string_decode(string $value, bool $autoType = true) 字符串解码.
 * @method static string string_encode(string|int|float $value, bool $autoType = true) 字符串编码.
 * @method static bool these(mixed $value, array $types)                               验证参数是否为指定的类型集合.
 * @method static bool type(mixed $value, string $type)                                验证 PHP 各种变量类型.
 */
class Type
{
    /**
     * 实现魔术方法 __callStatic.
     */
    public static function __callStatic(string $method, array $args): mixed
    {
        $helperClass = __NAMESPACE__.'\\Type\\'.ucfirst($method);
        return $helperClass::handle(...$args);
    }
}
