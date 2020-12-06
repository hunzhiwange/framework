<?php

declare(strict_types=1);

namespace Leevel\Support;

use function Leevel\Support\Str\un_camelize;
use Leevel\Support\Str\un_camelize;

/**
 * 类型判断辅助函数.
 *
 * @method static bool arr($data, array $types)    验证数组中的每一项类型是否正确.
 * @method static bool these($value, array $types) 验证参数是否为指定的类型集合.
 * @method static bool type($value, string $type)  验证 PHP 各种变量类型.
 */
class Type
{
    /**
     * 实现魔术方法 __callStatic.
     */
    public static function __callStatic(string $method, array $args): mixed
    {
        $fn = __NAMESPACE__.'\\Type\\'.un_camelize($method);
        if (!function_exists($fn)) {
            class_exists($fn);
        }

        return $fn(...$args);
    }
}

// import fn.
class_exists(un_camelize::class);
