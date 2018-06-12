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
     * @param mixed $mixString
     * @param bool  $bRecursive
     *
     * @return mixed
     */
    public static function stripslashes($mixString, $bRecursive = true)
    {
        if (true === $bRecursive and is_array($mixString)) { // 递归
            foreach ($mixString as $sKey => $mixValue) {
                $mixString[static::stripslashes($sKey)] = static::stripslashes($mixValue); // 如果你只注意到值，却没有注意到key
            }
        } else {
            if (is_string($mixString)) {
                $mixString = stripslashes($mixString);
            }
        }

        return $mixString;
    }

    /**
     * 添加模式转义.
     *
     * @param mixed  $mixString
     * @param string $bRecursive
     *
     * @return string
     */
    public static function addslashes($mixString, $bRecursive = true)
    {
        if (true === $bRecursive and is_array($mixString)) {
            foreach ($mixString as $sKey => $mixValue) {
                $mixString[static::addslashes($sKey)] = static::addslashes($mixValue); // 如果你只注意到值，却没有注意到key
            }
        } else {
            if (is_string($mixString)) {
                $mixString = addslashes($mixString);
            }
        }

        return $mixString;
    }

    /**
     * 深度过滤.
     *
     * @param array  $arrSearch
     * @param string $sSubject
     *
     * @return string
     */
    public static function deepReplace($arrSearch, $sSubject)
    {
        $bFound = true;
        $sSubject = (string) $sSubject;
        while ($bFound) {
            $bFound = false;
            foreach ((array) $arrSearch as $sVal) {
                while (false !== strpos($sSubject, $sVal)) {
                    $bFound = true;
                    $sSubject = str_replace($sVal, '', $sSubject);
                }
            }
        }

        return $sSubject;
    }

    /**
     * url 安全过滤.
     *
     * @param string $sUrl
     * @param array  $arrProtocols
     * @param bool   $booShow
     *
     * @return string
     */
    public static function escUrl($sUrl, $arrProtocols = null, $booShow = true)
    {
        $sOriginalUrl = $sUrl;
        if ('' == trim($sUrl)) {
            return $sUrl;
        }

        $sUrl = preg_replace('|[^a-z0-9-~+_.?#=!&;,/:%@$\|*\'()\\x80-\\xff]|i', '', $sUrl);
        $arrStrip = [
            '%0d',
            '%0a',
            '%0D',
            '%0A',
        ];
        $sUrl = static::deepReplace($arrStrip, $sUrl);
        $sUrl = str_replace(';//', '://', $sUrl); // 防止拼写错误

        // 加上 http:// ，防止导入一个脚本如 php，从而引发安全问题
        if (false === strpos($sUrl, ':') && '/' != substr($sUrl, 0, 1) && '#' != substr($sUrl, 0, 1) && ! preg_match('/^[a-z0-9-]+?\.php/i', $sUrl)) {
            $sUrl = 'http://' . $sUrl;
        }

        if (true === $booShow) {
            $sUrl = str_replace('&amp;', '&#038;', $sUrl);
            $sUrl = str_replace("'", '&#039;', $sUrl);
        }

        // 协议检查
        if (! is_array($arrProtocols)) {
            $arrProtocols = [
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

        return $sUrl;
    }

    /**
     * 过滤 script.
     *
     * @param sting $sStr
     *
     * @return string
     */
    public static function filterScript($sStr)
    {
        return preg_replace([
            '/<\s*script/',
            '/<\s*\/\s*script\s*>/',
            "/<\?/",
            "/\?>/",
        ], [
            '&lt;script',
            '&lt;/script&gt;',
            '&lt;?',
            '?&gt;',
        ], $sStr);
    }

    /**
     * 过滤十六进制字符串.
     *
     * @param stirng $sInput
     *
     * @return string
     */
    public static function cleanHex($sInput)
    {
        return preg_replace("![\][xX]([A-Fa-f0-9]{1,3})!", '', $sInput);
    }

    /**
     * sql 过滤.
     *
     * @param string $sStr
     *
     * @return string
     */
    public static function sqlFilter($sStr)
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
            "\(",
            "\)",
        ], '', $sStr);
    }

    /**
     * 字段过滤.
     *
     * @param mixed $mixFields
     *
     * @return mixed
     */
    public static function fieldsFilter($mixFields)
    {
        if (! is_array($mixFields)) {
            $mixFields = explode(',', $mixFields);
        }
        $mixFields = array_map(function ($str) {
            return safe::fieldsFilter(sqlFilter);
        }, $mixFields);
        $mixFields = implode(',', $mixFields);
        $mixFields = preg_replace('/^,|,$/', '', $mixFields);

        return $mixFields;
    }

    /**
     * 字符过滤.
     *
     * @param mixed $mixStrOrArray
     * @param int   $nMaxNum
     *
     * @return mixed
     */
    public function strFilter($mixStrOrArray, $nMaxNum = 20000)
    {
        if (is_array($mixStrOrArray)) {
            foreach ($mixStrOrArray as $sKey => $strVal) {
                $mixStrOrArray[static::strFilter($sKey)] = static::strFilter($strVal, $nMaxNum);
            }
        } else {
            $mixStrOrArray = trim(static::lengthLimit($mixStrOrArray, $nMaxNum));
            $mixStrOrArray = preg_replace('/&amp;((#(\d{3,5}|x[a-fA-F0-9]{4}));)/', '&\\1', static::htmlspecialchars($mixStrOrArray));
            $mixStrOrArray = str_replace('　', '', $mixStrOrArray);
        }

        return $mixStrOrArray;
    }

    /**
     * html 过滤.
     *
     * @param mixed $mixStrOrArray
     * @param int   $nMaxNum
     *
     * @return mixed
     */
    public function htmlFilter($mixStrOrArray, $nMaxNum = 20000)
    {
        if (is_array($mixStrOrArray)) {
            foreach ($mixStrOrArray as $sKey => $strVal) {
                $mixStrOrArray[static::htmlFilter($sKey)] = static::htmlFilter($strVal);
            }
        } else {
            $mixStrOrArray = trim(static::lengthLimit($mixStrOrArray, $nMaxNum));
            $mixStrOrArray = preg_replace([
                '/<\s*a[^>]*href\s*=\s*[\'\"]?(javascript|vbscript)[^>]*>/i',
                '/<([^>]*)on(\w)+=[^>]*>/i',
                '/<\s*\/?\s*(script|i?frame)[^>]*\s*>/i',
            ], [
                '<a href="#">',
                '<$1>',
                '&lt;$1&gt;',
            ], $mixStrOrArray);
            $mixStrOrArray = str_replace('　', '', $mixStrOrArray);
        }

        return $mixStrOrArray;
    }

    /**
     * int array 过滤.
     *
     * @param mixed $mixIdStr
     *
     * @return mixed
     */
    public static function intArrayFilter($mixIdStr)
    {
        if ('' != $mixIdStr) {
            if (! is_array($mixIdStr)) {
                $mixIdStr = explode(',', $mixIdStr);
            }
            $mixIdStr = array_map('intval', $mixIdStr);

            return implode(',', $mixIdStr);
        } else {
            return 0;
        }
    }

    /**
     * string array 过滤.
     *
     * @param mixed $mixStrOrArray
     *
     * @return mixed
     */
    public static function strArrayFilter($mixStrOrArray)
    {
        $sResult = '';
        if (! is_array($mixStrOrArray)) {
            $mixStrOrArray = explode(',', $mixStrOrArray);
        }
        $mixStrOrArray = array_map(function ($str) {
            return safe::fieldsFilter(sqlFilter);
        }, $mixStrOrArray);
        foreach ($StrOrArray as $sVal) {
            if ('' != $sVal) {
                $sResult .= "'" . $sVal . "',";
            }
        }

        return preg_replace('/,$/', '', $sResult);
    }

    /**
     * 访问时间限制.
     *
     * @param array $arrLimitTime
     */
    public static function limitTime(array $arrLimitTime)
    {
        if (empty($arrLimitTime)) {
            return;
        }

        $nLimitMinTime = strtotime($arrLimitTime[0]);
        $nLimitMaxTime = strtotime($arrLimitTime[1] ?? '');
        if (false === $nLimitMinTime || false === $nLimitMaxTime) {
            return;
        }

        if ($nLimitMaxTime < $nLimitMinTime) {
            $nLimitMaxTime = $nLimitMaxTime + 60 * 60 * 24;
        }

        if (time() >= $nLimitMinTime && time() <= $nLimitMaxTime) {
            throw new RuntimeException(sprintf('You can only before %s or after %s to access this.', date('Y-m-d H:i:s', $nLimitMinTime), date('Y-m-d H:i:s', $nLimitMaxTime)));
        }
    }

    /**
     * IP 访问限制.
     *
     * @param string $sVisitorIp
     * @param mixed  $mixLimitIp
     */
    public static function limitIp($sVisitorIp, $mixLimitIp)
    {
        if (! empty($mixLimitIp)) {
            if (is_string($mixLimitIp)) {
                $mixLimitIp = (array) $mixLimitIp;
            }

            foreach ($mixLimitIp as $sIp) {
                if (preg_match("/{$sIp}/", $sVisitorIp)) {
                    throw new RuntimeException(sprintf('You IP %s are banned,you can not access this', $sVisitorIp));
                }
            }
        }
    }

    /**
     * 检测代理.
     */
    public static function limitAgent()
    {
        if ($_SERVER['HTTP_X_FORWARDED_FOR'] || $_SERVER['HTTP_VIA'] || $_SERVER['HTTP_PROXY_CONNECTION'] || $_SERVER['HTTP_USER_AGENT_VIA']) {
            throw new RuntimeException('Proxy Connection denied.Your request was forbidden due to the administrator has set to deny all proxy connection.');
        }
    }

    /**
     * 过滤掉 javascript.
     *
     * @param string $sText 待过滤的字符串
     *
     * @return string
     */
    public static function cleanJs($sText)
    {
        $sText = trim($sText);
        $sText = stripslashes($sText);
        $sText = preg_replace('/<!--?.*-->/', '', $sText); // 完全过滤注释
        $sText = preg_replace('/<\?|\?>/', '', $sText); // 完全过滤动态代码
        $sText = preg_replace('/<script?.*\/script>/', '', $sText); // 完全过滤js
        $sText = preg_replace('/<\/?(html|head|meta|link|base|body|title|style|script|form|iframe|frame|frameset)[^><]*>/i', '', $sText); // 过滤多余html
        while (preg_match('/(<[^><]+)(lang|onfinish|onmouse|onexit|onerror|onclick|onkey|onload|onchange|onfocus|onblur)[^><]+/i', $sText, $arrMat)) { // 过滤on事件lang js
            $sText = str_replace($arrMat[0], $arrMat[1], $sText);
        }
        while (preg_match('/(<[^><]+)(window\.|javascript:|js:|about:|file:|document\.|vbs:|cookie)([^><]*)/i', $sText, $arrMat)) {
            $sText = str_replace($arrMat[0], $arrMat[1] . $arrMat[3], $sText);
        }

        return $sText;
    }

    /**
     * 字符串文本化.
     *
     * @param string $sText
     * @param bool   $booDeep
     * @param array  $arrWhite
     * @param array  $arrBlack
     *
     * @return string
     */
    public static function text($sText, $booDeep = true, $arrWhite = [], $arrBlack = [])
    {
        if (true === $booDeep) {
            $arrBlack = array_merge([
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
            ], $arrBlack);

            if ($arrWhite) {
                $arrTemp = [];
                foreach ($arrBlack as $sType) {
                    if (! in_array($sType, $arrWhite)) {
                        $arrTemp[] = $sType;
                    }
                }
                $arrBlack = $arrTemp;
            }
        } else {
            $arrBlack = [];
        }

        $sText = static::cleanJs($sText);
        $sText = preg_replace('/\s(?=\s)/', '', $sText); // 彻底过滤空格
        $sText = preg_replace('/[\n\r\t]/', ' ', $sText);
        if ($arrBlack) {
            $sText = str_replace($arrBlack, '', $sText);
        }
        $sText = strip_tags($sText);
        $sText = htmlspecialchars($sText);
        $sText = str_replace("'", '', $sText);

        return $sText;
    }

    /**
     * 字符过滤 JS和 HTML标签.
     *
     * @param string $sText
     *
     * @return string
     */
    public static function strip($sText)
    {
        $sText = trim($sText);
        $sText = static::cleanJs($sText);
        $sText = strip_tags($sText);

        return $sText;
    }

    /**
     * 字符 HTML 安全显示.
     *
     * @param string $sText
     *
     * @return string
     */
    public static function htmlView($sText)
    {
        $sText = stripslashes($sText);
        $sText = nl2br($sText);

        return $sText;
    }

    /**
     * 字符 HTML 安全实体.
     *
     * @param mixed $mixString
     *
     * @return string
     */
    public static function htmlspecialchars($mixString)
    {
        if (! is_array($mixString)) {
            $mixString = (array) $mixString;
        }

        $mixString = array_map(function ($sStr) {
            if (is_string($sStr)) {
                $sStr = htmlspecialchars(trim($sStr));
            }

            return $sStr;
        }, $mixString);

        if (1 == count($mixString)) {
            $mixString = reset($mixString);
        }

        return $mixString;
    }

    /**
     * 字符 HTML 实体还原
     *
     * @param mixed $mixString
     *
     * @return string
     */
    public static function unHtmlSpecialchars($mixString)
    {
        if (! is_array($mixString)) {
            $mixString = (array) $mixString;
        }

        $mixString = array_map(function ($sStr) {
            $sStr = strtr($sStr, array_flip(get_html_translation_table(HTML_SPECIALCHARS)));

            return $sStr;
        }, $mixString);

        if (1 == count($mixString)) {
            $mixString = reset($mixString);
        }

        return $mixString;
    }

    /**
     * 短字符串长度验证
     *
     * @param string $sStr
     * @param int    $nMaxLength
     *
     * @return mixed
     */
    public static function shortCheck($sStr, $nMaxLength = 500)
    {
        $sStr = self::lengthLimit($sStr, $nMaxLength);
        $sStr = str_replace([
            "\'",
            '\\',
            '#',
        ], '', $sStr);
        if ('' != $sStr) {
            $sStr = static::htmlspecialchars($sStr);
        }

        return preg_replace('/　+/', '', trim($sStr));
    }

    /**
     * 长字符串长度验证
     *
     * @param string $sPost
     * @param int    $nMaxLength
     *
     * @return mixed
     */
    public static function longCheck($sPost, $nMaxLength = 3000)
    {
        $sPost = static::lengthLimit($sPost, $nMaxLength);
        $sPost = str_replace("\'", '’', $sPost);
        $sPost = static::htmlspecialchars($sPost);
        $sPost = nl2br($sPost);

        return $sPost;
    }

    /**
     * 超长字符串长度验证
     *
     * @param string $sPost
     * @param int    $nMaxLength
     *
     * @return mixed
     */
    public static function bigCheck($sPost, $nMaxLength = 20000)
    {
        $sPost = self::lengthLimit($sPost, $nMaxLength);
        $sPost = str_replace("\'", '’', $sPost);
        $sPost = str_replace('<script ', '', $sPost);
        $sPost = str_replace('</script ', '', $sPost);

        return $sPost;
    }

    /**
     * 字符串长度限制.
     *
     * @param string $sStr
     * @param int    $nMaxSlen
     *
     * @return string
     */
    public static function lengthLimit($sStr, $nMaxSlen)
    {
        if (isset($sStr[$nMaxSlen])) {
            return ' ';
        } else {
            return $sStr;
        }
    }
}
