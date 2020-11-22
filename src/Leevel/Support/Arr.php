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
 * 数组辅助函数.
 *
 * @method static string convertJson($data = [], ?int $encodingOptions = null)                  转换 JSON 数据.
 * @method static array except(array &$input, array $filter)                                    返回黑名单排除后的数据.
 * @method static array filter(array &$input, array $rules)                                     返回过滤后的数据.
 * @method static array inCondition(array $data, $key, ?\Closure $filter = null)                数据库 IN 查询条件.
 * @method static mixed normalize($inputs, string $delimiter = ',', bool $allowedEmpty = false) 数组数据格式化.
 * @method static array only(array &$input, array $filter)                                      返回白名单过滤后的数据.
 * @method static bool shouldJson($content)                                                     是否可以转换为 JSON.
 */
class Arr
{
    /**
     * call.
     */
    public static function __callStatic(string $method, array $args): mixed
    {
        $fn = __NAMESPACE__.'\\Arr\\'.un_camelize($method);
        if (!function_exists($fn)) {
            class_exists($fn);
        }

        return $fn(...$args);
    }
}

// import fn.
class_exists(un_camelize::class);
