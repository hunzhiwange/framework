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

namespace Leevel\Support;

use function Leevel\Support\Str\un_camelize;
use Leevel\Support\Str\un_camelize;

/**
 * 字符串.
 *
 * @method static string camelize(string $value, string $separator = '_')                              下划线转驼峰.
 * @method static bool contains(string $toSearched, string $search)                                    判断字符串中是否包含给定的字符串集合.
 * @method static bool endsWith(string $toSearched, string $search)                                    判断字符串中是否包含给定的字符结尾.
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
 * @method static bool startsWith(string $toSearched, string $search)                                  判断字符串中是否包含给定的字符开始.
 * @method static string unCamelize(string $value, string $separator = '_')                            驼峰转下划线.
 */
class Str
{
    /**
     * call.
     *
     * @return mixed
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
