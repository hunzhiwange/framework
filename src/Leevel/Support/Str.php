<?php

declare(strict_types=1);

namespace Leevel\Support;

use function Leevel\Support\Str\un_camelize;
use Leevel\Support\Str\un_camelize;

/**
 * 字符串.
 *
 * @method static string camelize(string $value, string $separator = '_')                              下划线转驼峰.
 * @method static string formatBytes(int $fileSize, bool $withUnit = true)                             文件大小格式化.
 * @method static string formatDate(int $dateTemp, array $lang = [], string $dateFormat = 'Y-m-d H:i') 日期格式化.
 * @method static string randAlpha(int $length, ?string $charBox = null)                               随机字母.
 * @method static string randAlphaLowercase(int $length, ?string $charBox = null)                      随机小写字母.
 * @method static string randAlphaNum(int $length, ?string $charBox = null)                            随机字母数字.
 * @method static string randAlphaNumLowercase(int $length, ?string $charBox = null)                   随机小写字母数字.
 * @method static string randAlphaNumUppercase(int $length, ?string $charBox = null)                   随机大写字母数字.
 * @method static string randAlphaUppercase(int $length, ?string $charBox = null)                      随机大写字母.
 * @method static string randChinese(int $length, ?string $charBox = null)                             随机字中文.
 * @method static string randNum(int $length, ?string $charBox = null)                                 随机数字.
 * @method static string randStr(int $length, string $charBox)                                         随机字符串.
 * @method static string unCamelize(string $value, string $separator = '_')                            驼峰转下划线.
 */
class Str
{
    /**
     * 实现魔术方法 __callStatic.
     */
    public static function __callStatic(string $method, array $args): mixed
    {
        $fn = __NAMESPACE__.'\\Str\\'.un_camelize($method);
        if (!function_exists($fn)) {
            class_exists($fn);
        }

        return $fn(...$args);
    }
}

// import fn.
class_exists(un_camelize::class);
