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

namespace Tests\Encryption;

use Leevel\Encryption\Encryption;
use Leevel\Encryption\IEncryption;
use Tests\TestCase;

/**
 * encryption test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.07.11
 *
 * @version 1.0
 *
 * @api(
 *     title="加密解密",
 *     path="component/encryption",
 *     description="字符串加密解密支持。",
 * )
 */
class EncryptionTest extends TestCase
{
    protected function tearDown(): void
    {
        if (isset($GLOBALS['RUNTIME_ERROR_REPORTING'])) {
            error_reporting($GLOBALS['RUNTIME_ERROR_REPORTING']);
            unset($GLOBALS['RUNTIME_ERROR_REPORTING']);
        }
    }

    /**
     * @api(
     *     title="加密解密基本功能",
     *     description="",
     *     note="",
     * )
     */
    public function testBaseUse(): void
    {
        $encryption = new Encryption('encode-key');

        $this->assertInstanceof(IEncryption::class, $encryption);

        $sourceMessage = '123456';

        $encodeMessage = $encryption->encrypt($sourceMessage);

        $this->assertFalse($sourceMessage === $encodeMessage);

        $this->assertSame(
            $encryption->decrypt($encodeMessage),
            $sourceMessage
        );

        $this->assertSame(
            $encryption->decrypt($encodeMessage.'foo'),
            ''
        );

        $this->assertSame(
            'encode-key',
            $this->getTestProperty($encryption, 'key')
        );
    }

    /**
     * @api(
     *     title="加密解密 AES-128-CBC",
     *     description="",
     *     note="",
     * )
     */
    public function testUse128(): void
    {
        $encryption = new Encryption('encode-key', 'AES-128-CBC');

        $this->assertInstanceof(IEncryption::class, $encryption);

        $sourceMessage = '123456';

        $encodeMessage = $encryption->encrypt($sourceMessage);

        $this->assertFalse($sourceMessage === $encodeMessage);

        $this->assertSame(
            $encryption->decrypt($encodeMessage),
            $sourceMessage
        );

        $this->assertSame(
            $encryption->decrypt($encodeMessage.'foo'),
            ''
        );

        $this->assertSame(
            'encode-key',
            $this->getTestProperty($encryption, 'key')
        );
    }

    public function testEncryptCipherNotFound(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Encrypt cipher `foo` was not found.'
        );

        $encryption = new Encryption('encode-key', 'foo');
    }

    public function testDecryptWasEmpty(): void
    {
        $encryption = new Encryption('encode-key');

        $this->assertInstanceof(IEncryption::class, $encryption);

        $data = base64_encode('123456');

        $this->assertSame('', $encryption->decrypt($data));
    }

    public function testDecryptException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Decrypt the data failed.'
        );

        $encryption = new Encryption('encode-key');

        $this->assertInstanceof(IEncryption::class, $encryption);

        $vi = openssl_random_pseudo_bytes(openssl_cipher_iv_length('AES-256-CBC'));

        $data = base64_encode(base64_encode('123456')."\t".base64_encode($vi));

        $this->assertSame('', $encryption->decrypt($data));
    }

    /**
     * @api(
     *     title="加密解密支持过期时间",
     *     description="",
     *     note="",
     * )
     */
    public function testDecryptButExpired(): void
    {
        $encryption = new Encryption('encode-key');

        $this->assertInstanceof(IEncryption::class, $encryption);

        $data = $encryption->encrypt('123456', 1);

        $this->assertSame('123456', $encryption->decrypt($data));

        sleep(2);

        $this->assertSame('', $encryption->decrypt($data));
    }

    /**
     * @api(
     *     title="加密解密支持 RSA 校验",
     *     description="",
     *     note="",
     * )
     */
    public function testWithPublicAndPrimaryKey(): void
    {
        $encryption = new Encryption(
            'encode-key', 'AES-256-CBC',
            file_get_contents(__DIR__.'/assert/rsa_private_key.pem'),
            file_get_contents(__DIR__.'/assert/rsa_public_key.pem')
        );

        $this->assertInstanceof(IEncryption::class, $encryption);

        $sourceMessage = '123456';

        $encodeMessage = $encryption->encrypt($sourceMessage);

        $this->assertFalse($sourceMessage === $encodeMessage);

        $this->assertSame(
            $encryption->decrypt($encodeMessage),
            $sourceMessage
        );
    }

    public function testWithPrimaryKeyButPrimanyIsInvalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'openssl_sign(): supplied key param cannot be coerced into a private key'
        );

        $encryption = new Encryption(
            'encode-key', 'AES-256-CBC',
            'primary_key_not_found',
            file_get_contents(__DIR__.'/assert/rsa_public_key.pem')
        );

        $this->assertInstanceof(IEncryption::class, $encryption);

        $sourceMessage = '123456';

        $encryption->encrypt($sourceMessage);
    }

    public function testWithPublicKeyButPrimanyIsInvalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'openssl_verify(): supplied key param cannot be coerced into a public key'
        );

        $encryption = new Encryption(
            'encode-key', 'AES-256-CBC',
            file_get_contents(__DIR__.'/assert/rsa_private_key.pem'),
            'public_key_not_found'
        );

        $this->assertInstanceof(IEncryption::class, $encryption);

        $sourceMessage = '123456';

        $encodeMessage = $encryption->encrypt($sourceMessage);

        $this->assertFalse($sourceMessage === $encodeMessage);

        $this->assertSame(
            $encryption->decrypt($encodeMessage),
            $sourceMessage
        );
    }

    public function testUnpackDataFailed(): void
    {
        $encryption = new Encryption('encode-key');

        $result = $this->invokeTestMethod($encryption, 'unpackData', ['errordata']);

        $this->assertFalse($result);
    }

    public function testValidateDataForUnpackDataFailed(): void
    {
        $encryption = new Encryption('encode-key');

        $result = $this->invokeTestMethod($encryption, 'validateData', ['errordata', '']);

        $this->assertSame($result, '');
    }

    public function testValidateDataForBase64DecodeFailed(): void
    {
        $encryption = new Encryption('encode-key');
        $expiry = '0000000000';

        // 返回 false 例子
        // https://www.php.net/manual/vote-note.php?id=118801&page=function.base64-decode&vote=down
        $value = '$VGhpcyBpcyBhbiBlbmNvZGVkIHN0cmluZw==';

        $iv = 'testiv';
        $sign = '';

        $data = implode("\t", [$expiry, $value, $iv, $sign]);

        $result = $this->invokeTestMethod($encryption, 'validateData', [$data, 'testiv']);
        $this->assertSame($result, '');
    }

    public function testNormalizeSignFailed(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Openssl sign failed.'
        );

        $GLOBALS['RUNTIME_ERROR_REPORTING'] = error_reporting();
        error_reporting(0);

        $errorRsaPrivateKey = str_replace(
            '-----END PRIVATE KEY-----',
            'error'.PHP_EOL.'-----END PRIVATE KEY-----',
            file_get_contents(__DIR__.'/assert/rsa_private_key.pem')
        );

        $encryption = new Encryption(
            'encode-key', 'AES-256-CBC',
            $errorRsaPrivateKey,
            file_get_contents(__DIR__.'/assert/rsa_public_key.pem')
        );

        $data = 'data';
        $this->invokeTestMethod($encryption, 'normalizeSign', [$data]);
    }

    public function testValidateSignFailed(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Openssl verify sign failed.'
        );

        $GLOBALS['RUNTIME_ERROR_REPORTING'] = error_reporting();
        error_reporting(0);

        $errorRsaPublicKey = str_replace(
            '-----END PRIVATE KEY-----',
            'error'.PHP_EOL.'-----END PRIVATE KEY-----',
            file_get_contents(__DIR__.'/assert/rsa_public_key.pem')
        );

        $encryption = new Encryption(
            'encode-key', 'AES-256-CBC',
            file_get_contents(__DIR__.'/assert/rsa_private_key.pem'),
            $errorRsaPublicKey
        );

        $data = 'data';
        $sign = '';
        $this->invokeTestMethod($encryption, 'validateSign', [$data, $sign]);
    }
}
