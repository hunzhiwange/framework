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
     * 创建一个加密实例.
     *
     * @param string $key
     * @param int    $expiry
     */
    public function __construct($key, int $expiry = 0)
    {
        $this->key = (string) $key;
        $this->expiry = (int) $expiry;
    }

    /**
     * 加密.
     *
     * @param string     $value
     * @param null|mixed $expiry
     *
     * @return string
     */
    public function encrypt($value, $expiry = null)
    {
        return $this->authcode(
            $value,
            false,
            $this->key,
            null !== $expiry ? $expiry : $this->expiry
        );
    }

    /**
     * 解密.
     *
     * @param string $value
     *
     * @return string
     */
    public function decrypt($value)
    {
        return $this->authcode(
            $value,
            true,
            $this->key
        );
    }

    /**
     * 来自 Discuz 经典 PHP 加密算法.
     *
     * @param string $strings
     * @param bool   $decode
     * @param string $key
     * @param int    $expiry
     *
     * @return string
     */
    protected function authcode(string $strings, bool $decode = true, string $key = '', int $expiry = 0)
    {
        $ckeyLength = 4;
        $key = md5($key ?: '');

        $keya = md5(substr($key, 0, 16));
        $keyb = md5(substr($key, 16, 16));
        $keyc = $ckeyLength ?
            ($decode ?
                substr($strings, 0, $ckeyLength) :
                substr(md5(microtime()), -$ckeyLength)) :
            '';

        $cryptkey = $keya.md5($keya.$keyc);
        $keyLength = strlen($cryptkey);

        $strings = $decode ?
            base64_decode(substr($strings, $ckeyLength), true) :
            sprintf('%010d', $expiry ? $expiry + time() : 0).
                substr(md5($strings.$keyb), 0, 16).
                $strings;

        $result = '';
        $stringsLength = strlen($strings);
        $box = range(0, 255);
        $rndkey = [];

        for ($i = 0; $i <= 255; $i++) {
            $rndkey[$i] = ord($cryptkey[$i % $keyLength]);
        }

        for ($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }

        for ($a = $j = $i = 0; $i < $stringsLength; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            $result .= chr(ord($strings[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }

        if ($decode) {
            if ((0 === substr($result, 0, 10) || substr($result, 0, 10) - time() > 0) &&
                substr($result, 10, 16) === substr(md5(substr($result, 26).$keyb), 0, 16)) {
                return substr($result, 26);
            }

            return '';
        }

        return $keyc.str_replace('=', '', base64_encode($result));
    }
}
