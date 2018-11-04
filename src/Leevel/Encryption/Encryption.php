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

use InvalidArgumentException;
use Throwable;

/**
 * 加密组件.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.04.05
 *
 * @version 1.0
 */
class Encryption implements IEncryption
{
    /**
     * 加密 key.
     *
     * @var string
     */
    protected $key;

    /**
     * openssl 加密解密算法.
     *
     * @var string
     */
    protected $cipher;

    /**
     * 安全 RSA 私钥.
     *
     * @var string
     */
    protected $rsaPrivate;

    /**
     * 安全 RSA 公钥.
     *
     * @var string
     */
    protected $rsaPublic;

    /**
     * 构造函数.
     *
     * @param string $key
     * @param string $cipher
     */
    public function __construct(string $key, string $cipher = 'AES-256-CBC', string $rsaPrivate = '', string $rsaPublic = '')
    {
        if (!in_array($cipher, openssl_get_cipher_methods(), true)) {
            throw new InvalidArgumentException(
                sprintf('Encrypt cipher `%s` was not found.', $cipher)
            );
        }

        $this->key = $key;
        $this->cipher = $cipher;
        $this->rsaPrivate = $rsaPrivate;
        $this->rsaPublic = $rsaPublic;
    }

    /**
     * 加密.
     *
     * @param string $value
     * @param int    $expiry
     *
     * @return string
     */
    public function encrypt(string $value, int $expiry = 0): string
    {
        if ($this->rsaPrivate) {
            $rsaPrivate = openssl_pkey_get_private($this->rsaPrivate);

            try {
                if (openssl_sign($value, $sign, $rsaPrivate)) {
                    $sign = base64_encode($sign);
                } else {
                    throw new InvalidArgumentException('Openssl sign failed.'); // @codeCoverageIgnore
                }
            } catch (Throwable $e) {
                throw new InvalidArgumentException($e->getMessage());
            }
        } else {
            $sign = '';
        }

        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($this->cipher));
        $expiry = sprintf('%010d', $expiry ? $expiry + time() : 0);
        $value = $expiry."\t".base64_encode($value)."\t".base64_encode($iv)."\t".$sign;

        $value = openssl_encrypt($value, $this->cipher, $this->key, OPENSSL_RAW_DATA, $iv);

        if (false === $value) {
            throw new InvalidArgumentException('Encrypt the data failed.'); // @codeCoverageIgnore
        }

        return base64_encode(base64_encode($value)."\t".base64_encode($iv));
    }

    /**
     * 解密.
     *
     * @param string $value
     *
     * @return string
     */
    public function decrypt(string $value): string
    {
        $value = base64_decode($value, true);

        if (false === $value) {
            return '';
        }

        $value = explode("\t", $value);

        if (2 !== count($value)) {
            return '';
        }

        $data = openssl_decrypt(
            base64_decode($value[0], true), $this->cipher, $this->key, OPENSSL_RAW_DATA, base64_decode($value[1], true)
        );

        if (false === $data) {
            throw new InvalidArgumentException('Decrypt the data failed.');
        }

        $data = explode("\t", $data);

        if (4 !== count($data) || $data[2] !== $value[1] ||
            ('0000000000' !== $data[0] && time() > $data[0])) {
            return '';
        }

        $result = base64_decode($data[1], true) ?: '';

        if (!$result || !$this->rsaPublic) {
            return $result;
        }

        $rsaPrivate = openssl_pkey_get_public($this->rsaPublic);

        try {
            if (1 === openssl_verify($result, base64_decode($data[3], true), $rsaPrivate)) {
                return $result;
            }

            throw new InvalidArgumentException('Openssl verify sign failed.'); // @codeCoverageIgnore
        } catch (Throwable $e) {
            throw new InvalidArgumentException($e->getMessage());
        }
    }
}
