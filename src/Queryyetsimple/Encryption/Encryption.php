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

/**
 * 加密组件.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.04.05
 *
 * @version 1.0
 */
class Encryption extends Connect implements IEncryption
{
    /**
     * 创建一个加密应用.
     *
     * @param string $strKey
     * @param int    $intExpiry
     */
    public function __construct($strKey, $intExpiry = 0)
    {
        $this->strKey = (string) $strKey;
        $this->intExpiry = (int) $intExpiry;
    }

    /**
     * 加密.
     *
     * @param string     $strValue
     * @param null|mixed $intExpiry
     *
     * @return string
     */
    public function encrypt($strValue, $intExpiry = null)
    {
        return $this->authcode($strValue, false, $this->strKey, null !== $intExpiry ? $intExpiry : $this->intExpiry);
    }

    /**
     * 解密.
     *
     * @param string $strValue
     *
     * @return string
     */
    public function decrypt($strValue)
    {
        return $this->authcode($strValue, true, $this->strKey);
    }

    /**
     * 来自 Discuz 经典 PHP 加密算法.
     *
     * @param string $string
     * @param bool   $operation
     * @param string $key
     * @param number $expiry
     *
     * @return string
     */
    protected function authcode($string, $operation = true, $key = null, $expiry = 0)
    {
        $ckey_length = 4;

        $key = md5($key);
        $keya = md5(substr($key, 0, 16));
        $keyb = md5(substr($key, 16, 16));
        $keyc = $ckey_length ? (true === $operation ? substr($string, 0, $ckey_length) : substr(md5(microtime()), -$ckey_length)) : '';

        $cryptkey = $keya.md5($keya.$keyc);
        $key_length = strlen($cryptkey);
        $string = true === $operation ? base64_decode(substr($string, $ckey_length), true) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
        $string_length = strlen($string);

        $result = '';
        $box = range(0, 255);
        $rndkey = [];
        for ($i = 0; $i <= 255; ++$i) {
            $rndkey[$i] = ord($cryptkey[$i % $key_length]);
        }

        for ($j = $i = 0; $i < 256; ++$i) {
            $j = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }

        for ($a = $j = $i = 0; $i < $string_length; ++$i) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }

        if (true === $operation) {
            if ((0 === substr($result, 0, 10) || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) === substr(md5(substr($result, 26).$keyb), 0, 16)) {
                return substr($result, 26);
            }

            return '';
        }

        return $keyc.str_replace('=', '', base64_encode($result));
    }
}
