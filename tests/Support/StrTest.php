<?php

declare(strict_types=1);

namespace Tests\Support;

use Leevel\Kernel\Utils\Api;
use Leevel\Support\Str;
use Tests\TestCase;

#[Api([
    'zh-CN:title' => '字符串',
    'path' => 'component/support/str',
    'zh-CN:description' => <<<'EOT'
这里为系统提供的字符串使用的功能文档说明。
EOT,
])]
final class StrTest extends TestCase
{
    #[Api([
        'zh-CN:title' => '随机字母数字',
    ])]
    public function testBaseUse(): void
    {
        self::assertSame('', Str::randAlphaNum(0));

        self::assertTrue(
            1 === preg_match('/^[A-Za-z0-9]+$/', Str::randAlphaNum(4))
        );

        self::assertTrue(
            1 === preg_match('/^[A-Za-z0-9]+$/', Str::randAlphaNum(4, 'AB4cdef'))
        );

        self::assertTrue(
            4 === \strlen(Str::randAlphaNum(4))
        );
    }

    #[Api([
        'zh-CN:title' => '随机小写字母和数字',
        'zh-CN:description' => <<<'EOT'
利用本方法可以生成随机数小写字母。
EOT,
        'zh-CN:note' => <<<'EOT'
支持位数和指定字符范围
EOT,
    ])]
    public function testRandAlphaNumLowercase(): void
    {
        self::assertSame('', Str::randAlphaNumLowercase(0));

        self::assertTrue(
            1 === preg_match('/^[a-z0-9]+$/', Str::randAlphaNumLowercase(4))
        );

        self::assertTrue(
            1 === preg_match('/^[a-z0-9]+$/', Str::randAlphaNumLowercase(4, 'cdefghigk2450'))
        );

        self::assertTrue(
            4 === \strlen(Str::randAlphaNumLowercase(4))
        );
    }

    #[Api([
        'zh-CN:title' => '随机大写字母和数字',
        'zh-CN:description' => <<<'EOT'
利用本方法可以生成随机数大写字母。
EOT,
        'zh-CN:note' => <<<'EOT'
支持位数和指定字符范围
EOT,
    ])]
    public function testRandAlphaNumUppercase(): void
    {
        self::assertSame('', Str::randAlphaNumUppercase(0));

        self::assertTrue(
            1 === preg_match('/^[A-Z0-9]+$/', Str::randAlphaNumUppercase(4))
        );

        self::assertTrue(
            1 === preg_match('/^[A-Z0-9]+$/', Str::randAlphaNumUppercase(4, 'ABCDEFG1245'))
        );

        self::assertTrue(
            4 === \strlen(Str::randAlphaNumUppercase(4))
        );
    }

    #[Api([
        'zh-CN:title' => '随机字母',
        'zh-CN:description' => <<<'EOT'
利用本方法可以生成随机字母。
EOT,
        'zh-CN:note' => <<<'EOT'
支持位数和指定字符范围
EOT,
    ])]
    public function testRandAlpha(): void
    {
        self::assertSame('', Str::randAlpha(0));

        self::assertTrue(
            1 === preg_match('/^[A-Za-z]+$/', Str::randAlpha(4))
        );

        self::assertTrue(
            1 === preg_match('/^[A-Za-z]+$/', Str::randAlpha(4, 'ABCDEFGefijk'))
        );

        self::assertTrue(
            4 === \strlen(Str::randAlpha(4))
        );
    }

    #[Api([
        'zh-CN:title' => '随机小写字母',
    ])]
    public function testRandAlphaLowercase(): void
    {
        self::assertSame('', Str::randAlphaLowercase(0));

        self::assertTrue(
            1 === preg_match('/^[a-z]+$/', Str::randAlphaLowercase(4))
        );

        self::assertTrue(
            1 === preg_match('/^[a-z]+$/', Str::randAlphaLowercase(4, 'cdefghigk'))
        );

        self::assertTrue(
            4 === \strlen(Str::randAlphaLowercase(4))
        );
    }

    #[Api([
        'zh-CN:title' => '随机大写字母',
    ])]
    public function testRandAlphaUppercase(): void
    {
        self::assertSame('', Str::randAlphaUppercase(0));

        self::assertTrue(
            1 === preg_match('/^[A-Z]+$/', Str::randAlphaUppercase(4))
        );

        self::assertTrue(
            1 === preg_match('/^[A-Z]+$/', Str::randAlphaUppercase(4, 'ABCDEFG'))
        );

        self::assertTrue(
            4 === \strlen(Str::randAlphaUppercase(4))
        );
    }

    #[Api([
        'zh-CN:title' => '随机数字',
    ])]
    public function testRandNum(): void
    {
        self::assertSame('', Str::randNum(0));

        self::assertTrue(
            1 === preg_match('/^[0-9]+$/', Str::randNum(4))
        );

        self::assertTrue(
            1 === preg_match('/^[0-9]+$/', Str::randNum(4, '012456'))
        );

        self::assertTrue(
            4 === \strlen(Str::randNum(4))
        );
    }

    #[Api([
        'zh-CN:title' => '随机字中文',
    ])]
    public function testRandChinese(): void
    {
        self::assertSame('', Str::randChinese(0));

        self::assertTrue(
            1 === preg_match('/^[\x{4e00}-\x{9fa5}]+$/u', Str::randChinese(4))
        );

        self::assertTrue(
            1 === preg_match('/^[\x{4e00}-\x{9fa5}]+$/u', Str::randChinese(4, '我是一个中国人'))
        );

        self::assertTrue(
            12 === \strlen(Str::randChinese(4))
        );
    }

    #[Api([
        'zh-CN:title' => '随机字符串',
    ])]
    public function testRandStr(): void
    {
        self::assertSame('', Str::randStr(0, ''));
        self::assertSame('', Str::randStr(5, ''));

        self::assertTrue(
            4 === \strlen(Str::randStr(4, 'helloworld'))
        );
    }

    #[Api([
        'zh-CN:title' => '日期格式化',
    ])]
    public function testFormatDate(): void
    {
        $time = time();

        self::assertSame(date('Y-m-d H:i', $time + 600), Str::formatDate($time + 600));
        self::assertSame(date('Y-m-d', $time + 600), Str::formatDate($time + 600, [], 'Y-m-d'));

        self::assertSame(date('Y-m-d', $time - 1286400), Str::formatDate($time - 1286400, [], 'Y-m-d'));

        self::assertSame('1 hours ago', Str::formatDate($time - 5040));
        self::assertSame('1 小时之前', Str::formatDate($time - 5040, ['hours' => '小时之前']));

        self::assertSame('4 minutes ago', Str::formatDate($time - 240));
        self::assertSame('4 分钟之前', Str::formatDate($time - 240, ['minutes' => '分钟之前']));

        self::assertTrue(\in_array(Str::formatDate($time - 40), ['40 seconds ago', '41 seconds ago', '42 seconds ago'], true));
        self::assertTrue(\in_array(Str::formatDate($time - 40, ['seconds' => '秒之前']), ['40 秒之前', '41 秒之前', '42 秒之前'], true));
    }

    #[Api([
        'zh-CN:title' => '文件大小格式化',
    ])]
    public function testFormatBytes(): void
    {
        self::assertSame('2.4G', Str::formatBytes(2573741824));
        self::assertSame('2.4', Str::formatBytes(2573741824, false));

        self::assertSame('4.81M', Str::formatBytes(5048576));
        self::assertSame('4.81', Str::formatBytes(5048576, false));

        self::assertSame('2.95K', Str::formatBytes(3024));
        self::assertSame('2.95', Str::formatBytes(3024, false));

        self::assertSame('100B', Str::formatBytes(100));
        self::assertSame('100', Str::formatBytes(100, false));
    }

    #[Api([
        'zh-CN:title' => '下划线转驼峰',
    ])]
    public function testCamelize(): void
    {
        self::assertSame('helloWorld', Str::camelize('helloWorld'));
        self::assertSame('helloWorld', Str::camelize('helloWorld', '-'));
        self::assertSame('helloWorld', Str::camelize('hello_world'));
        self::assertSame('helloWorld', Str::camelize('hello-world', '-'));
        self::assertSame('he3lloWorld', Str::camelize('he3llo_world'));
        self::assertSame('hello3World', Str::camelize('hello3_world'));
    }

    #[Api([
        'zh-CN:title' => '驼峰转下划线',
    ])]
    public function testUnCamelize(): void
    {
        self::assertSame('hello_world', Str::unCamelize('hello_world'));
        self::assertSame('hello-world', Str::unCamelize('hello-world', '-'));
        self::assertSame('hello_world', Str::unCamelize('helloWorld'));
        self::assertSame('hello-world', Str::unCamelize('helloWorld', '-'));
        self::assertSame('hello2_world', Str::unCamelize('hello2World'));
        self::assertSame('hel2lo_world', Str::unCamelize('hel2loWorld'));
    }

    public function testStrNotFound(): void
    {
        $this->expectException(\Error::class);
        $this->expectExceptionMessage('Class "Leevel\\Support\\Str\\NotFound" not found');

        self::assertTrue(Str::notFound());
    }
}
