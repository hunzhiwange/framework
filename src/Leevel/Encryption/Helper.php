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

namespace Leevel\Encryption;

use function Leevel\Support\Str\un_camelize;
use Leevel\Support\Str\un_camelize;

/**
 * 助手类.
 *
 * @method static string cleanHex(string $strings)                                   过滤十六进制字符串.
 * @method static string cleanJs(string $strings)                                    过滤 JavaScript.
 * @method static customAddslashes($data, bool $recursive = true)                    添加模式转义.
 * @method static customHtmlspecialchars($data)                                      字符 HTML 安全实体.
 * @method static customStripslashes($data, bool $recursive = true)                  移除魔术方法转义.
 * @method static string deepReplace(array $search, string $subject)                 深度过滤.
 * @method static string filterScript(string $strings)                               过滤 script.
 * @method static htmlFilter($data)                                                  HTML 过滤.
 * @method static string htmlView(string $strings)                                   字符 HTML 安全显示.
 * @method static strFilter($data)                                                   字符过滤.
 * @method static string strip(string $strings)                                      字符过滤 JS 和 HTML 标签.
 * @method static string text(string $strings, bool $deep = true, array $black = []) 字符串文本化.
 * @method static unHtmlspecialchars($data)                                          字符 HTML 实体还原.
 */
class Helper
{
    /**
     * call.
     *
     * @return mixed
     */
    public static function __callStatic(string $method, array $args)
    {
        $fn = __NAMESPACE__.'\\Helper\\'.un_camelize($method);
        if (!function_exists($fn)) {
            class_exists($fn);
        }

        return $fn(...$args);
    }
}

// import fn.
class_exists(un_camelize::class);
