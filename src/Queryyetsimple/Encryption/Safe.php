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
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\Encryption;

use RuntimeException;

/**
 * 安全函数.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.04.05
 *
 * @version 1.0
 */
class Safe
{
    /**
     * 移除魔术方法转义.
     *
     * @param mixed $strings
     * @param bool  $recursive
     *
     * @return mixed
     */
    public static function stripslashes($strings, $recursive = true)
    {
        if (true === $recursive and is_array($strings)) { // 递归
            foreach ($strings as $key => $value) {
                $strings[static::stripslashes($key)] = static::stripslashes($value); // 如果你只注意到值，却没有注意到key
            }
        } else {
            if (is_string($strings)) {
                $strings = stripslashes($strings);
            }
        }

        return $strings;
    }

    /**
     * 添加模式转义.
     *
     * @param mixed  $strings
     * @param string $recursive
     *
     * @return string
     */
    public static function addslashes($strings, $recursive = true)
    {
        if (true === $recursive and is_array($strings)) {
            foreach ($strings as $key => $value) {
                $strings[static::addslashes($key)] = static::addslashes($value); // 如果你只注意到值，却没有注意到key
            }
        } else {
            if (is_string($strings)) {
                $strings = addslashes($strings);
            }
        }

        return $strings;
    }

    /**
     * 深度过滤.
     *
     * @param array  $search
     * @param string $subject
     *
     * @return string
     */
    public static function deepReplace(array $search, $subject)
    {
        $found = true;
        $subject = (string) $subject;

        while ($found) {
            $found = false;

            foreach ((array) $search as $val) {
                while (false !== strpos($subject, $val)) {
                    $found = true;
                    $subject = str_replace($val, '', $subject);
                }
            }
        }

        return $subject;
    }

    /**
     * url 安全过滤.
     *
     * @param string $url
     * @param array  $protocols
     * @param bool   $show
     *
     * @return string
     */
    public static function escUrl($url, $protocols = null, $show = true)
    {
        $originalUrl = $url;

        if ('' === trim($url)) {
            return $url;
        }

        $url = preg_replace('|[^a-z0-9-~+_.?#=!&;,/:%@$\|*\'()\\x80-\\xff]|i', '', $url);

        $strip = [
            '%0d',
            '%0a',
            '%0D',
            '%0A',
        ];

        $url = static::deepReplace($strip, $url);
        $url = str_replace(';//', '://', $url); // 防止拼写错误

        // 加上 http:// ，防止导入一个脚本如 php，从而引发安全问题
        if (false === strpos($url, ':') &&
            '/' !== substr($url, 0, 1) &&
            '#' !== substr($url, 0, 1) &&
            !preg_match('/^[a-z0-9-]+?\.php/i', $url)) {
            $url = 'http://'.$url;
        }

        if (true === $show) {
            $url = str_replace('&amp;', '&#038;', $url);
            $url = str_replace("'", '&#039;', $url);
        }

        // 协议检查
        if (!is_array($protocols)) {
            $protocols = [
                'http',
                'https',
                'ftp',
                'ftps',
                'mailto',
                'news',
                'irc',
                'gopher',
                'nntp',
                'feed',
                'telnet',
                'mms',
                'rtsp',
                'svn',
            ];
        }

        return $url;
    }

    /**
     * 过滤 script.
     *
     * @param sting $strings
     *
     * @return string
     */
    public static function filterScript($strings)
    {
        return preg_replace([
            '/<\s*script/',
            '/<\s*\/\s*script\s*>/',
            '/<\\?/',
            '/\\?>/',
        ], [
            '&lt;script',
            '&lt;/script&gt;',
            '&lt;?',
            '?&gt;',
        ], $strings);
    }

    /**
     * 过滤十六进制字符串.
     *
     * @param stirng $strings
     *
     * @return string
     */
    public static function cleanHex($strings)
    {
        return preg_replace('![\\][xX]([A-Fa-f0-9]{1,3})!', '', $strings);
    }

    /**
     * sql 过滤.
     *
     * @param string $strings
     *
     * @return string
     */
    public static function sqlFilter($strings)
    {
        return str_replace([
            '/',
            '\\',
            "'",
            '#',
            ' ',
            '  ',
            '%',
            '&',
            '\\(',
            '\\)',
        ], '', $strings);
    }

    /**
     * 字段过滤.
     *
     * @param mixed $fields
     *
     * @return mixed
     */
    public static function fieldsFilter($fields)
    {
        if (!is_array($fields)) {
            $fields = explode(',', $fields);
        }

        $fields = array_map(function ($str) {
            return static::sqlFilter($str);
        }, $fields);

        $fields = implode(',', $fields);
        $fields = preg_replace('/^,|,$/', '', $fields);

        return $fields;
    }

    /**
     * 字符过滤.
     *
     * @param mixed $strings
     * @param int   $maxNum
     *
     * @return mixed
     */
    public function strFilter($strings, $maxNum = 20000)
    {
        if (is_array($strings)) {
            foreach ($strings as $key => $val) {
                $strings[static::strFilter($key)] = static::strFilter($val, $maxNum);
            }
        } else {
            $strings = trim(static::lengthLimit($strings, $maxNum));

            $strings = preg_replace(
                '/&amp;((#(\d{3,5}|x[a-fA-F0-9]{4}));)/',
                '&\\1',
                static::htmlspecialchars($strings)
            );

            $strings = str_replace('　', '', $strings);
        }

        return $strings;
    }

    /**
     * html 过滤.
     *
     * @param mixed $strings
     * @param int   $maxNum
     *
     * @return mixed
     */
    public function htmlFilter($strings, $maxNum = 20000)
    {
        if (is_array($strings)) {
            foreach ($strings as $key => $val) {
                $strings[static::htmlFilter($key)] = static::htmlFilter($val);
            }
        } else {
            $strings = trim(static::lengthLimit($strings, $maxNum));

            $strings = preg_replace([
                '/<\s*a[^>]*href\s*=\s*[\'\"]?(javascript|vbscript)[^>]*>/i',
                '/<([^>]*)on(\w)+=[^>]*>/i',
                '/<\s*\/?\s*(script|i?frame)[^>]*\s*>/i',
            ], [
                '<a href="#">',
                '<$1>',
                '&lt;$1&gt;',
            ], $strings);

            $strings = str_replace('　', '', $strings);
        }

        return $strings;
    }

    /**
     * int array 过滤.
     *
     * @param mixed $strings
     *
     * @return mixed
     */
    public static function intArrayFilter($strings)
    {
        if ('' !== $strings) {
            if (!is_array($strings)) {
                $strings = explode(',', $strings);
            }

            $strings = array_map('intval', $strings);

            return implode(',', $strings);
        }

        return 0;
    }

    /**
     * string array 过滤.
     *
     * @param mixed $strings
     *
     * @return mixed
     */
    public static function strArrayFilter($strings)
    {
        $result = '';

        if (!is_array($strings)) {
            $strings = explode(',', $strings);
        }

        $strings = array_map(function ($str) {
            return static::sqlFilter($str);
        }, $strings);

        foreach ($strings as $val) {
            if ('' !== $val) {
                $result .= "'".$val."',";
            }
        }

        return preg_replace('/,$/', '', $result);
    }

    /**
     * 访问时间限制.
     *
     * @param array $limitTime
     */
    public static function limitTime(array $limitTime)
    {
        if (empty($limitTime)) {
            return;
        }

        $limitMinTime = strtotime($limitTime[0]);
        $limitMaxTime = strtotime($limitTime[1] ?? '');

        if (false === $limitMinTime || false === $limitMaxTime) {
            return;
        }

        if ($limitMaxTime < $limitMinTime) {
            $limitMaxTime = $limitMaxTime + 60 * 60 * 24;
        }

        if (time() >= $limitMinTime && time() <= $limitMaxTime) {
            throw new RuntimeException(
                sprintf(
                    'You can only before %s or after %s to access this.',
                    date('Y-m-d H:i:s', $limitMinTime),
                    date('Y-m-d H:i:s', $limitMaxTime)
                )
            );
        }
    }

    /**
     * IP 访问限制.
     *
     * @param string $visitorIp
     * @param mixed  $limitIp
     */
    public static function limitIp($visitorIp, $limitIp)
    {
        if (!empty($limitIp)) {
            if (is_string($limitIp)) {
                $limitIp = (array) $limitIp;
            }

            foreach ($limitIp as $ip) {
                if (preg_match("/{$ip}/", $visitorIp)) {
                    throw new RuntimeException(
                        sprintf(
                            'You IP %s are banned,you can not access this',
                            $visitorIp
                        )
                    );
                }
            }
        }
    }

    /**
     * 检测代理.
     */
    public static function limitAgent()
    {
        if ($_SERVER['HTTP_X_FORWARDED_FOR'] ||
            $_SERVER['HTTP_VIA'] ||
            $_SERVER['HTTP_PROXY_CONNECTION'] ||
            $_SERVER['HTTP_USER_AGENT_VIA']) {
            throw new RuntimeException(
                'Proxy Connection denied.Your request was forbidden due to the '.
                'administrator has set to deny all proxy connection.'
            );
        }
    }

    /**
     * 过滤掉 javascript.
     *
     * @param string $strings 待过滤的字符串
     *
     * @return string
     */
    public static function cleanJs($strings)
    {
        $strings = trim($strings);

        $strings = stripslashes($strings);
        $strings = preg_replace('/<!--?.*-->/', '', $strings); // 完全过滤注释
        $strings = preg_replace('/<\?|\?>/', '', $strings); // 完全过滤动态代码
        $strings = preg_replace('/<script?.*\/script>/', '', $strings); // 完全过滤js

        $strings = preg_replace('/<\/?(html|head|meta|link|base|body|title|style|script|form|iframe|frame|frameset)[^><]*>/i', '', $strings); // 过滤多余html

        while (preg_match('/(<[^><]+)(lang|onfinish|onmouse|onexit|onerror|onclick|onkey|onload|onchange|onfocus|onblur)[^><]+/i', $strings, $matches)) { // 过滤on事件lang js
            $strings = str_replace($matches[0], $matches[1], $strings);
        }

        while (preg_match('/(<[^><]+)(window\.|javascript:|js:|about:|file:|document\.|vbs:|cookie)([^><]*)/i', $strings, $matches)) {
            $strings = str_replace($matches[0], $matches[1].$matches[3], $strings);
        }

        return $strings;
    }

    /**
     * 字符串文本化.
     *
     * @param string $strings
     * @param bool   $deep
     * @param array  $white
     * @param array  $black
     *
     * @return string
     */
    public static function strings($strings, $deep = true, $white = [], $black = [])
    {
        if (true === $deep) {
            $black = array_merge([
                ' ',
                '&nbsp;',
                '&',
                '=',
                '-',
                '#',
                '%',
                '!',
                '@',
                '^',
                '*',
                'amp;',
            ], $black);

            if ($white) {
                $temp = [];

                foreach ($black as $type) {
                    if (!in_array($type, $white, true)) {
                        $temp[] = $type;
                    }
                }

                $black = $temp;
            }
        } else {
            $black = [];
        }

        $strings = static::cleanJs($strings);
        $strings = preg_replace('/\s(?=\s)/', '', $strings); // 彻底过滤空格
        $strings = preg_replace('/[\n\r\t]/', ' ', $strings);

        if ($black) {
            $strings = str_replace($black, '', $strings);
        }

        $strings = strip_tags($strings);
        $strings = htmlspecialchars($strings);
        $strings = str_replace("'", '', $strings);

        return $strings;
    }

    /**
     * 字符过滤 JS和 HTML标签.
     *
     * @param string $strings
     *
     * @return string
     */
    public static function strip($strings)
    {
        $strings = trim($strings);
        $strings = static::cleanJs($strings);
        $strings = strip_tags($strings);

        return $strings;
    }

    /**
     * 字符 HTML 安全显示.
     *
     * @param string $strings
     *
     * @return string
     */
    public static function htmlView($strings)
    {
        $strings = stripslashes($strings);
        $strings = nl2br($strings);

        return $strings;
    }

    /**
     * 字符 HTML 安全实体.
     *
     * @param mixed $strings
     *
     * @return string
     */
    public static function htmlspecialchars($strings)
    {
        if (!is_array($strings)) {
            $strings = (array) $strings;
        }

        $strings = array_map(function ($strings) {
            if (is_string($strings)) {
                $strings = htmlspecialchars(trim($strings));
            }

            return $strings;
        }, $strings);

        if (1 === count($strings)) {
            $strings = reset($strings);
        }

        return $strings;
    }

    /**
     * 字符 HTML 实体还原
     *
     * @param mixed $strings
     *
     * @return string
     */
    public static function unHtmlSpecialchars($strings)
    {
        if (!is_array($strings)) {
            $strings = (array) $strings;
        }

        $strings = array_map(function ($strings) {
            $strings = strtr($strings, array_flip(get_html_translation_table(HTML_SPECIALCHARS)));

            return $strings;
        }, $strings);

        if (1 === count($strings)) {
            $strings = reset($strings);
        }

        return $strings;
    }

    /**
     * 短字符串长度验证
     *
     * @param string $strings
     * @param int    $maxLength
     *
     * @return mixed
     */
    public static function shortCheck($strings, $maxLength = 500)
    {
        $strings = static::lengthLimit($strings, $maxLength);

        $strings = str_replace([
            "\\'",
            '\\',
            '#',
        ], '', $strings);
        if ('' !== $strings) {
            $strings = static::htmlspecialchars($strings);
        }

        return preg_replace('/　+/', '', trim($strings));
    }

    /**
     * 长字符串长度验证
     *
     * @param string $strings
     * @param int    $maxLength
     *
     * @return mixed
     */
    public static function longCheck($strings, $maxLength = 3000)
    {
        $strings = static::lengthLimit($strings, $maxLength);
        $strings = str_replace("\\'", '’', $strings);
        $strings = static::htmlspecialchars($strings);
        $strings = nl2br($strings);

        return $strings;
    }

    /**
     * 超长字符串长度验证
     *
     * @param string $strings
     * @param int    $maxLength
     *
     * @return mixed
     */
    public static function bigCheck(string $strings, int $maxLength = 20000)
    {
        $strings = self::lengthLimit($strings, $maxLength);
        $strings = str_replace("\\'", '’', $strings);
        $strings = str_replace('<script ', '', $strings);
        $strings = str_replace('</script ', '', $strings);

        return $strings;
    }

    /**
     * 字符串长度限制.
     *
     * @param string $strings
     * @param int    $maxLength
     *
     * @return string
     */
    public static function lengthLimit(string $strings, int $maxLength)
    {
        if (isset($strings[$maxLength])) {
            return ' ';
        }

        return $strings;
    }
}
