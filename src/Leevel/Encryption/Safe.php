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
 * (c) 2010-2019 http://queryphp.com All rights reserved.
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
    public static function stripslashes($strings, bool $recursive = true)
    {
        if (true === $recursive and is_array($strings)) { // 递归
            $result = [];

            foreach ($strings as $key => $value) {
                // 如果你只注意到值，却没有注意到 key
                $result[static::stripslashes($key)] = static::stripslashes($value);
            }

            return $result;
        }
        if (is_string($strings)) {
            $strings = stripslashes($strings);
        }

        return $strings;
    }

    /**
     * 添加模式转义.
     *
     * @param mixed $strings
     * @param bool  $recursive
     *
     * @return string
     */
    public static function addslashes($strings, bool $recursive = true)
    {
        if (true === $recursive and is_array($strings)) {
            $result = [];

            foreach ($strings as $key => $value) {
                // 如果你只注意到值，却没有注意到 key
                $result[static::addslashes($key)] = static::addslashes($value);
            }

            return $result;
        }
        if (is_string($strings)) {
            $strings = addslashes($strings);
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
    public static function deepReplace(array $search, string $subject)
    {
        $found = true;

        while ($found) {
            $found = false;

            foreach ($search as $val) {
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
    public static function escUrl(string $url, ?array $protocols = null, bool $show = true)
    {
        $originalUrl = $url;

        if ('' === trim($url)) {
            return $url;
        }

        $url = preg_replace('|[^a-z0-9-~+_.?#=!&;,/:%@$\|*\'()\\x80-\\xff]|i', '', $url);

        // 回车编码
        $url = static::deepReplace([
            '%0d',
            '%0a',
            '%0D',
            '%0A',
        ], $url);

        // 防止拼写错误
        $url = str_replace(';//', '://', $url);

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
    public static function strFilter($strings, int $maxNum = 20000)
    {
        if (is_array($strings)) {
            $result = [];

            foreach ($strings as $key => $val) {
                $result[static::strFilter($key)] = static::strFilter($val, $maxNum);
            }

            return $result;
        }
        $strings = trim(static::lengthLimit((string) ($strings), $maxNum));

        $strings = preg_replace(
                '/&amp;((#(\d{3,5}|x[a-fA-F0-9]{4}));)/',
                '&\\1',
                static::htmlspecialchars($strings)
            );

        $strings = str_replace('　', '', $strings);

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
    public static function htmlFilter($strings, $maxNum = 20000)
    {
        if (is_array($strings)) {
            $result = [];

            foreach ($strings as $key => $val) {
                $result[static::htmlFilter($key)] = static::htmlFilter($val, $maxNum);
            }

            return $result;
        }
        $strings = trim(static::lengthLimit((string) ($strings), $maxNum));

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

        return $strings;
    }

    /**
     * int array 过滤.
     *
     * @param mixed $strings
     *
     * @return mixed
     */
    public static function intArrFilter($strings)
    {
        if ('' === $strings) {
            return 0;
        }

        if (!is_array($strings)) {
            $strings = explode(',', $strings);
        }

        $strings = array_map('intval', $strings);

        return implode(',', $strings);
    }

    /**
     * string array 过滤.
     *
     * @param mixed $strings
     *
     * @return mixed
     */
    public static function strArrFilter($strings)
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
     * @param int   $time
     */
    public static function limitTime(array $limitTime, int $time)
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
            $limitMaxTime += 60 * 60 * 24;
        }

        if ($time < $limitMinTime || $time > $limitMaxTime) {
            return;
        }

        throw new RuntimeException(
            sprintf(
                'You can only before %s or after %s to access this.',
                date('Y-m-d H:i:s', $limitMinTime),
                date('Y-m-d H:i:s', $limitMaxTime)
            )
        );
    }

    /**
     * IP 访问限制.
     *
     * @param string $visitorIp
     * @param array  $limitIp
     */
    public static function limitIp(string $visitorIp, array $limitIp)
    {
        if (empty($limitIp)) {
            return;
        }

        foreach ($limitIp as $ip) {
            if (!preg_match("/{$ip}/", $visitorIp)) {
                continue;
            }

            throw new RuntimeException(
                sprintf(
                    'You IP %s are banned,you cannot access this.',
                    $visitorIp
                )
            );
        }
    }

    /**
     * 检测代理.
     */
    public static function limitAgent()
    {
        if (!isset($_SERVER['HTTP_X_FORWARDED_FOR']) &&
            !isset($_SERVER['HTTP_VIA']) &&
            !isset($_SERVER['HTTP_PROXY_CONNECTION']) &&
            !isset($_SERVER['HTTP_USER_AGENT_VIA'])) {
            return;
        }

        throw new RuntimeException(
            'Proxy Connection denied.Your request was forbidden due to the '.
            'administrator has set to deny all proxy connection.'
        );
    }

    /**
     * 过滤掉 javascript.
     *
     * @param string $strings 待过滤的字符串
     *
     * @return string
     */
    public static function cleanJs(string $strings)
    {
        $strings = trim($strings);

        $strings = stripslashes($strings);
        $strings = preg_replace('/<!--?.*-->/', '', $strings); // 完全过滤注释
        $strings = preg_replace('/<\?|\?>/', '', $strings); // 完全过滤动态代码
        $strings = preg_replace('/<script?.*\/script>/', '', $strings); // 完全过滤 js

        $strings = preg_replace('/<\/?(html|head|meta|link|base|body|title|style|script|form|iframe|frame|frameset)[^><]*>/i', '', $strings); // 过滤多余 html

        while (preg_match('/(<[^><]+)(lang|onfinish|onmouse|onexit|onerror|onclick|onkey|onload|onchange|onfocus|onblur)[^><]+/i', $strings, $matches)) { // 过滤 on 事件
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
     * @param array  $black
     *
     * @return string
     */
    public static function text(string $strings, bool $deep = true, array $black = [])
    {
        if (true === $deep && !$black) {
            $black = [
                ' ', '&nbsp;', '&', '=', '-',
                '#', '%', '!', '@', '^', '*', 'amp;',
            ];
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
     * 字符过滤 JS 和 HTML 标签.
     *
     * @param string $strings
     *
     * @return string
     */
    public static function strip(string $strings)
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
    public static function htmlView(string $strings)
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
            $strings = strtr(
                $strings,
                array_flip(
                    get_html_translation_table(HTML_SPECIALCHARS)
                )
            );

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
    public static function shortCheck(string $strings, int $maxLength = 500)
    {
        $strings = static::lengthLimit($strings, $maxLength);
        $strings = str_replace(["\\'", '\\', '#'], '', $strings);
        $strings = static::htmlspecialchars($strings);

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
    public static function longCheck(string $strings, int $maxLength = 3000)
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

    /**
     * 签名算法.
     *
     * @param string $query
     * @param string $secret
     * @param array  $ignore
     *
     * @return string
     */
    public static function signature(array $query, string $secret, array $ignore = []): string
    {
        foreach ($ignore as $v) {
            if (isset($query[$v])) {
                unset($query[$v]);
            }
        }

        ksort($query);

        $sign = '';

        foreach ($query as $k => $v) {
            if (!is_array($v)) {
                $sign .= $k.'='.$v;
            } else {
                $sign .= $k.self::signature($v, $secret, $ignore);
            }
        }

        return hash_hmac('sha256', $sign, $secret);
    }
}
