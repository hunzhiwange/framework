<?php

declare(strict_types=1);

namespace Tests\Support;

use Leevel\Kernel\Utils\Api;
use Leevel\Support\Str\Camelize;
use Leevel\Support\Str\FormatBytes;
use Leevel\Support\Str\FormatDate;
use Leevel\Support\Str\RandAlpha;
use Leevel\Support\Str\RandAlphaLowercase;
use Leevel\Support\Str\RandAlphaNum;
use Leevel\Support\Str\RandAlphaNumLowercase;
use Leevel\Support\Str\RandAlphaNumUppercase;
use Leevel\Support\Str\RandAlphaUppercase;
use Leevel\Support\Str\RandChinese;
use Leevel\Support\Str\RandNum;
use Leevel\Support\Str\RandStr;
use Leevel\Support\Str\UnCamelize;
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
        self::assertSame('', RandAlphaNum::handle(0));

        self::assertTrue(
            1 === preg_match('/^[A-Za-z0-9]+$/', RandAlphaNum::handle(4))
        );

        self::assertTrue(
            1 === preg_match('/^[A-Za-z0-9]+$/', RandAlphaNum::handle(4, 'AB4cdef'))
        );

        self::assertTrue(
            4 === \strlen(RandAlphaNum::handle(4))
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
        self::assertSame('', RandAlphaNumLowercase::handle(0));

        self::assertTrue(
            1 === preg_match('/^[a-z0-9]+$/', RandAlphaNumLowercase::handle(4))
        );

        self::assertTrue(
            1 === preg_match('/^[a-z0-9]+$/', RandAlphaNumLowercase::handle(4, 'cdefghigk2450'))
        );

        self::assertTrue(
            4 === \strlen(RandAlphaNumLowercase::handle(4))
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
        self::assertSame('', RandAlphaNumUppercase::handle(0));

        self::assertTrue(
            1 === preg_match('/^[A-Z0-9]+$/', RandAlphaNumUppercase::handle(4))
        );

        self::assertTrue(
            1 === preg_match('/^[A-Z0-9]+$/', RandAlphaNumUppercase::handle(4, 'ABCDEFG1245'))
        );

        self::assertTrue(
            4 === \strlen(RandAlphaNumUppercase::handle(4))
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
        self::assertSame('', RandAlpha::handle(0));

        self::assertTrue(
            1 === preg_match('/^[A-Za-z]+$/', RandAlpha::handle(4))
        );

        self::assertTrue(
            1 === preg_match('/^[A-Za-z]+$/', RandAlpha::handle(4, 'ABCDEFGefijk'))
        );

        self::assertTrue(
            4 === \strlen(RandAlpha::handle(4))
        );
    }

    #[Api([
        'zh-CN:title' => '随机小写字母',
    ])]
    public function testRandAlphaLowercase(): void
    {
        self::assertSame('', RandAlphaLowercase::handle(0));

        self::assertTrue(
            1 === preg_match('/^[a-z]+$/', RandAlphaLowercase::handle(4))
        );

        self::assertTrue(
            1 === preg_match('/^[a-z]+$/', RandAlphaLowercase::handle(4, 'cdefghigk'))
        );

        self::assertTrue(
            4 === \strlen(RandAlphaLowercase::handle(4))
        );
    }

    #[Api([
        'zh-CN:title' => '随机大写字母',
    ])]
    public function testRandAlphaUppercase(): void
    {
        self::assertSame('', RandAlphaUppercase::handle(0));

        self::assertTrue(
            1 === preg_match('/^[A-Z]+$/', RandAlphaUppercase::handle(4))
        );

        self::assertTrue(
            1 === preg_match('/^[A-Z]+$/', RandAlphaUppercase::handle(4, 'ABCDEFG'))
        );

        self::assertTrue(
            4 === \strlen(RandAlphaUppercase::handle(4))
        );
    }

    #[Api([
        'zh-CN:title' => '随机数字',
    ])]
    public function testRandNum(): void
    {
        self::assertSame('', RandNum::handle(0));

        self::assertTrue(
            1 === preg_match('/^[0-9]+$/', RandNum::handle(4))
        );

        self::assertTrue(
            1 === preg_match('/^[0-9]+$/', RandNum::handle(4, '012456'))
        );

        self::assertTrue(
            4 === \strlen(RandNum::handle(4))
        );
    }

    #[Api([
        'zh-CN:title' => '随机字中文',
    ])]
    public function testRandChinese(): void
    {
        self::assertSame('', RandChinese::handle(0));

        self::assertTrue(
            1 === preg_match('/^[\x{4e00}-\x{9fa5}]+$/u', RandChinese::handle(4))
        );

        self::assertTrue(
            1 === preg_match('/^[\x{4e00}-\x{9fa5}]+$/u', RandChinese::handle(4, '我是一个中国人'))
        );

        self::assertTrue(
            12 === \strlen(RandChinese::handle(4))
        );
    }

    #[Api([
        'zh-CN:title' => '随机字符串',
    ])]
    public function testRandStr(): void
    {
        self::assertSame('', RandStr::handle(0, ''));
        self::assertSame('', RandStr::handle(5, ''));

        self::assertTrue(
            4 === \strlen(RandStr::handle(4, 'helloworld'))
        );
    }

    #[Api([
        'zh-CN:title' => '日期格式化',
    ])]
    public function testFormatDate(): void
    {
        $time = time();

        self::assertSame(date('Y-m-d H:i', $time + 600), FormatDate::handle($time + 600));
        self::assertSame(date('Y-m-d', $time + 600), FormatDate::handle($time + 600, [], 'Y-m-d'));

        self::assertSame(date('Y-m-d', $time - 1286400), FormatDate::handle($time - 1286400, [], 'Y-m-d'));

        self::assertSame('1 hours ago', FormatDate::handle($time - 5040));
        self::assertSame('1 小时之前', FormatDate::handle($time - 5040, ['hours' => '小时之前']));

        self::assertSame('4 minutes ago', FormatDate::handle($time - 240));
        self::assertSame('4 分钟之前', FormatDate::handle($time - 240, ['minutes' => '分钟之前']));

        self::assertTrue(\in_array(FormatDate::handle($time - 40), ['40 seconds ago', '41 seconds ago', '42 seconds ago'], true));
        self::assertTrue(\in_array(FormatDate::handle($time - 40, ['seconds' => '秒之前']), ['40 秒之前', '41 秒之前', '42 秒之前'], true));
    }

    #[Api([
        'zh-CN:title' => '文件大小格式化',
    ])]
    public function testFormatBytes(): void
    {
        self::assertSame('2.4G', FormatBytes::handle(2573741824));
        self::assertSame('2.4', FormatBytes::handle(2573741824, false));

        self::assertSame('4.81M', FormatBytes::handle(5048576));
        self::assertSame('4.81', FormatBytes::handle(5048576, false));

        self::assertSame('2.95K', FormatBytes::handle(3024));
        self::assertSame('2.95', FormatBytes::handle(3024, false));

        self::assertSame('100B', FormatBytes::handle(100));
        self::assertSame('100', FormatBytes::handle(100, false));
    }

    #[Api([
        'zh-CN:title' => '下划线转驼峰',
    ])]
    public function testCamelize(): void
    {
        self::assertSame('helloWorld', Camelize::handle('helloWorld'));
        self::assertSame('helloWorld', Camelize::handle('helloWorld', '-'));
        self::assertSame('helloWorld', Camelize::handle('hello_world'));
        self::assertSame('helloWorld', Camelize::handle('hello-world', '-'));
        self::assertSame('he3lloWorld', Camelize::handle('he3llo_world'));
        self::assertSame('hello3World', Camelize::handle('hello3_world'));
    }

    #[Api([
        'zh-CN:title' => '驼峰转下划线',
    ])]
    public function testUnCamelize(): void
    {
        self::assertSame('hello_world', UnCamelize::handle('hello_world'));
        self::assertSame('hello-world', UnCamelize::handle('hello-world', '-'));
        self::assertSame('hello_world', UnCamelize::handle('helloWorld'));
        self::assertSame('hello-world', UnCamelize::handle('helloWorld', '-'));
        self::assertSame('hello2_world', UnCamelize::handle('hello2World'));
        self::assertSame('hel2lo_world', UnCamelize::handle('hel2loWorld'));
    }
}
