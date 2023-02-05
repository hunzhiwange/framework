<?php

declare(strict_types=1);

namespace Leevel\Encryption;

/**
 * 助手类.
 *
 * @method static string cleanHex(string $strings)                                   过滤十六进制字符串.
 * @method static string cleanJs(string $strings)                                    过滤 JavaScript.
 * @method static mixed  customAddslashes($data, bool $recursive = true)             添加模式转义.
 * @method static mixed  customHtmlspecialchars($data)                               字符 HTML 安全实体.
 * @method static mixed  customStripslashes($data, bool $recursive = true)           移除魔术方法转义.
 * @method static string deepReplace(array $search, string $subject)                 深度过滤.
 * @method static string filterScript(string $strings)                               过滤脚本.
 * @method static mixed  htmlFilter($data)                                           HTML 过滤.
 * @method static string htmlView(string $strings)                                   字符 HTML 安全显示.
 * @method static mixed  strFilter($data)                                            字符过滤.
 * @method static string strip(string $strings)                                      字符过滤 JS 和 HTML 标签.
 * @method static string text(string $strings, bool $deep = true, array $black = []) 字符串文本化.
 * @method static mixed  unHtmlspecialchars($data)                                   字符 HTML 实体还原.
 */
class Helper
{
    /**
     * 实现魔术方法 __callStatic.
     */
    public static function __callStatic(string $method, array $args): mixed
    {
        $helperClass = __NAMESPACE__.'\\Helper\\'.ucfirst($method);

        return $helperClass::handle(...$args);
    }
}
