<?php

declare(strict_types=1);

namespace Leevel\Encryption;

/**
 * 加密组件.
 */
class Encryption implements IEncryption
{
    /**
     * 加密 key.
     */
    protected string $key;

    /**
     * openssl 加密解密算法.
     */
    protected string $cipher;

    /**
     * 安全 RSA 私钥.
     */
    protected ?string $rsaPrivate = null;

    /**
     * 安全 RSA 公钥.
     */
    protected ?string $rsaPublic = null;

    /**
     * 构造函数.
     */
    public function __construct(string $key, string $cipher = 'AES-256-CBC', ?string $rsaPrivate = null, ?string $rsaPublic = null)
    {
        $this->validateCipher($cipher);
        $this->key = $key;
        $this->cipher = $cipher;
        $this->rsaPrivate = $rsaPrivate;
        $this->rsaPublic = $rsaPublic;
    }

    /**
     * {@inheritDoc}
     */
    public function encrypt(string $value, int $expiry = 0): string
    {
        $iv = $this->createIv();
        $value = $this->packData($value, $expiry, $iv);

        return $this->encryptData($value, $iv);
    }

    /**
     * {@inheritDoc}
     */
    public function decrypt(string $value): string|false
    {
        if (false === ($value = $this->decryptData($value))) {
            return false;
        }
        [$data, $iv] = $value;

        return $this->validateData($data, $iv);
    }

    /**
     * 打包数据.
     */
    protected function packData(string $value, int $expiry, string $iv): string
    {
        $data = [
            $this->normalizeExpiry($expiry),
            base64_encode($value),
            base64_encode($iv),
            $this->normalizeSign($value),
        ];

        return implode("\t", $data);
    }

    /**
     * 解包数据.
     */
    protected function unpackData(string $value): array|false
    {
        $data = explode("\t", $value);
        if (4 !== \count($data)) {
            return false;
        }

        $key = ['expiry', 'value', 'iv', 'sign'];

        return array_combine($key, $data);
    }

    /**
     * 加密数据.
     *
     * @throws \InvalidArgumentException
     */
    protected function encryptData(string $value, string $iv): string
    {
        $value = openssl_encrypt($value, $this->cipher, $this->key, OPENSSL_RAW_DATA, $iv);
        if (false === $value) {
            // 在 error_reporting(0) 场景下 openssl 加密解密算法 cipher 错误的情况下才会执行
            throw new \InvalidArgumentException('Encrypt the data failed.');
        }

        return $this->packDataWithIv($value, $iv);
    }

    /**
     * 解密数据.
     *
     * @throws \InvalidArgumentException
     */
    protected function decryptData(string $value): array|false
    {
        if (false === ($value = base64_decode($value, true))) {
            return false;
        }

        if (false === ($value = $this->unpackDataWithIv($value))) {
            return false;
        }

        $data = openssl_decrypt(
            $value['value'],
            $this->cipher,
            $this->key,
            OPENSSL_RAW_DATA,
            $value['iv']
        );

        if (false === $data) {
            throw new \InvalidArgumentException('Decrypt the data failed.');
        }

        return [$data, base64_encode($value['iv'])];
    }

    /**
     * 数据加入向量并打包.
     */
    protected function packDataWithIv(string $value, string $iv): string
    {
        return base64_encode(base64_encode($value)."\t".base64_encode($iv));
    }

    /**
     * 解包带向量的数据.
     */
    protected function unpackDataWithIv(string $value): array|false
    {
        $data = explode("\t", $value);
        if (2 !== \count($data)) {
            return false;
        }

        $key = ['value', 'iv'];
        // @phpstan-ignore-next-line
        $data[0] = base64_decode($data[0], true);
        // @phpstan-ignore-next-line
        $data[1] = base64_decode($data[1], true);

        return array_combine($key, $data);
    }

    /**
     * 格式化过期时间.
     */
    protected function normalizeExpiry(int $expiry = 0): string
    {
        return sprintf('%010d', $expiry ? $expiry + time() : 0);
    }

    /**
     * 创建初始化向量.
     *
     * @throws \Exception
     */
    protected function createIv(): string
    {
        $length = openssl_cipher_iv_length($this->cipher);
        if (false === $length) {
            throw new \Exception('Gets the cipher iv length failed.');
        }

        return openssl_random_pseudo_bytes($length);
    }

    /**
     * 获取签名.
     *
     * @throws \InvalidArgumentException
     */
    protected function normalizeSign(string $value): string
    {
        if (!$this->rsaPrivate) {
            return '';
        }

        try {
            $rsaPrivate = openssl_pkey_get_private($this->rsaPrivate);
            // @phpstan-ignore-next-line
            if (openssl_sign($value, $sign, $rsaPrivate)) {
                return base64_encode($sign);
            }

            // 在 error_reporting(0) 场景下签名 $rsaPrivate 错误的情况下才会执行
            throw new \InvalidArgumentException('Openssl sign failed.');
        } catch (\Throwable $e) {
            throw new \InvalidArgumentException($e->getMessage());
        }
    }

    /**
     * 校验加密算法.
     *
     * @throws \InvalidArgumentException
     */
    protected function validateCipher(string $cipher): void
    {
        if (!\in_array(strtolower($cipher), openssl_get_cipher_methods(), true)) {
            throw new \InvalidArgumentException(sprintf('Encrypt cipher `%s` was not found.', $cipher));
        }
    }

    /**
     * 校验数据正确性.
     */
    protected function validateData(string $data, string $iv): string|false
    {
        if (false === ($data = $this->unpackData($data))) {
            return false;
        }

        if ($data['iv'] !== $iv
            || ('0000000000' !== $data['expiry'] && time() > $data['expiry'])) {
            return false;
        }

        $result = base64_decode($data['value'], true) ?: false;
        if (false === $result) {
            return false;
        }

        return $this->validateSign($result, $data['sign']);
    }

    /**
     * 验证签名.
     *
     * @throws \InvalidArgumentException
     */
    protected function validateSign(string $value, string $sign): string
    {
        if (!$this->rsaPublic) {
            return $value;
        }

        try {
            $rsaPublic = openssl_pkey_get_public($this->rsaPublic);
            // @phpstan-ignore-next-line
            if (1 === openssl_verify($value, base64_decode($sign, true), $rsaPublic)) {
                return $value;
            }

            // 在 error_reporting(0) 场景下签名 $rsaPublic 错误的情况下才会执行
            throw new \InvalidArgumentException('Openssl verify sign failed.');
        } catch (\Throwable $e) {
            throw new \InvalidArgumentException($e->getMessage());
        }
    }
}
