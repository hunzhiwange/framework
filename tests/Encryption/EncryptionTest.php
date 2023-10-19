<?php

declare(strict_types=1);

namespace Tests\Encryption;

use Leevel\Encryption\Encryption;
use Leevel\Encryption\IEncryption;
use Tests\TestCase;

#[Api([
    'zh-CN:title' => '加密解密',
    'path' => 'component/encryption',
    'zh-CN:description' => <<<'EOT'
字符串加密解密支持。
EOT,
])]
final class EncryptionTest extends TestCase
{
    protected function tearDown(): void
    {
        if (isset($GLOBALS['RUNTIME_ERROR_REPORTING'])) {
            error_reporting($GLOBALS['RUNTIME_ERROR_REPORTING']);
            unset($GLOBALS['RUNTIME_ERROR_REPORTING']);
        }
    }

    #[Api([
        'zh-CN:title' => '加密解密基本功能',
    ])]
    public function testBaseUse(): void
    {
        $encryption = new Encryption('encode-key');

        $this->assertInstanceof(IEncryption::class, $encryption);

        $sourceMessage = '123456';

        $encodeMessage = $encryption->encrypt($sourceMessage);

        static::assertFalse($sourceMessage === $encodeMessage);

        static::assertSame(
            $encryption->decrypt($encodeMessage),
            $sourceMessage
        );

        static::assertSame(
            $encryption->decrypt($encodeMessage.'foo'),
            false
        );

        static::assertSame(
            'encode-key',
            $this->getTestProperty($encryption, 'key')
        );
    }

    #[Api([
        'zh-CN:title' => '加密解密 AES-128-CBC',
    ])]
    public function testUse128(): void
    {
        $encryption = new Encryption('encode-key', 'AES-128-CBC');

        $this->assertInstanceof(IEncryption::class, $encryption);

        $sourceMessage = '123456';

        $encodeMessage = $encryption->encrypt($sourceMessage);

        static::assertFalse($sourceMessage === $encodeMessage);

        static::assertSame(
            $encryption->decrypt($encodeMessage),
            $sourceMessage
        );

        static::assertSame(
            $encryption->decrypt($encodeMessage.'foo'),
            false
        );

        static::assertSame(
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

        static::assertFalse($encryption->decrypt($data));
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

        static::assertSame('', $encryption->decrypt($data));
    }

    #[Api([
        'zh-CN:title' => '加密解密支持过期时间',
    ])]
    public function testDecryptButExpired(): void
    {
        $encryption = new Encryption('encode-key');

        $this->assertInstanceof(IEncryption::class, $encryption);

        $data = $encryption->encrypt('123456', 1);

        static::assertSame('123456', $encryption->decrypt($data));

        sleep(2);

        static::assertFalse($encryption->decrypt($data));
    }

    #[Api([
        'zh-CN:title' => '加密解密支持 RSA 校验',
    ])]
    public function testWithPublicAndPrimaryKey(): void
    {
        $encryption = new Encryption(
            'encode-key',
            'AES-256-CBC',
            file_get_contents(__DIR__.'/assert/rsa_private_key.pem'),
            file_get_contents(__DIR__.'/assert/rsa_public_key.pem')
        );

        $this->assertInstanceof(IEncryption::class, $encryption);

        $sourceMessage = '123456';

        $encodeMessage = $encryption->encrypt($sourceMessage);

        static::assertFalse($sourceMessage === $encodeMessage);

        static::assertSame(
            $encryption->decrypt($encodeMessage),
            $sourceMessage
        );
    }

    public function testWithPrimaryKeyButPrimaryIsInvalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'openssl_sign(): Supplied key param cannot be coerced into a private key'
        );

        $encryption = new Encryption(
            'encode-key',
            'AES-256-CBC',
            'primary_key_not_found',
            file_get_contents(__DIR__.'/assert/rsa_public_key.pem')
        );

        $this->assertInstanceof(IEncryption::class, $encryption);

        $sourceMessage = '123456';

        $encryption->encrypt($sourceMessage);
    }

    public function testWithPublicKeyButPrimaryIsInvalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'openssl_verify(): Supplied key param cannot be coerced into a public key'
        );

        $encryption = new Encryption(
            'encode-key',
            'AES-256-CBC',
            file_get_contents(__DIR__.'/assert/rsa_private_key.pem'),
            'public_key_not_found'
        );

        $this->assertInstanceof(IEncryption::class, $encryption);

        $sourceMessage = '123456';

        $encodeMessage = $encryption->encrypt($sourceMessage);

        static::assertFalse($sourceMessage === $encodeMessage);

        static::assertSame(
            $encryption->decrypt($encodeMessage),
            $sourceMessage
        );
    }

    public function testUnpackDataFailed(): void
    {
        $encryption = new Encryption('encode-key');

        $result = $this->invokeTestMethod($encryption, 'unpackData', ['errordata']);

        static::assertFalse($result);
    }

    public function testValidateDataForUnpackDataFailed(): void
    {
        $encryption = new Encryption('encode-key');

        $result = $this->invokeTestMethod($encryption, 'validateData', ['errordata', '']);

        static::assertSame($result, false);
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
        static::assertSame($result, false);
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
            'encode-key',
            'AES-256-CBC',
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
            'encode-key',
            'AES-256-CBC',
            file_get_contents(__DIR__.'/assert/rsa_private_key.pem'),
            $errorRsaPublicKey
        );

        $data = 'data';
        $sign = '';
        $this->invokeTestMethod($encryption, 'validateSign', [$data, $sign]);
    }

    public function testEncryptDataFailed(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Encrypt the data failed.'
        );

        $GLOBALS['RUNTIME_ERROR_REPORTING'] = error_reporting();
        error_reporting(0);

        $encryption = new Encryption(
            'encode-key',
            'AES-256-CBC'
        );

        $value = 'data';
        $iv = $this->invokeTestMethod($encryption, 'createIv');
        $this->setTestProperty($encryption, 'cipher', 11);
        $this->invokeTestMethod($encryption, 'encryptData', [$value, $iv]);
    }

    public function test1(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(
            'Gets the cipher iv length failed.'
        );

        $GLOBALS['RUNTIME_ERROR_REPORTING'] = error_reporting();
        error_reporting(0);

        $encryption = new Encryption(
            'encode-key',
            'AES-256-CBC'
        );

        $this->invokeTestMethod($encryption, 'createIv');
        $this->setTestProperty($encryption, 'cipher', 'not found');
        $this->invokeTestMethod($encryption, 'createIv');
    }
}
