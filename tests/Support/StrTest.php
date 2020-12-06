<?php

declare(strict_types=1);

namespace Tests\Support;

use Leevel\Support\Str;
use Tests\TestCase;

/**
 * @api(
 *     zh-CN:title="字符串",
 *     path="component/support/str",
 *     zh-CN:description="这里为系统提供的字符串使用的功能文档说明。",
 * )
 */
class StrTest extends TestCase
{
    /**
     * @api(
     *     zh-CN:title="随机字母数字",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testBaseUse(): void
    {
        $this->assertSame('', Str::randAlphaNum(0));

        $this->assertTrue(
            1 === preg_match('/^[A-Za-z0-9]+$/', Str::randAlphaNum(4))
        );

        $this->assertTrue(
            1 === preg_match('/^[A-Za-z0-9]+$/', Str::randAlphaNum(4, 'AB4cdef'))
        );

        $this->assertTrue(
            4 === strlen(Str::randAlphaNum(4))
        );
    }

    /**
     * @api(
     *     zh-CN:title="随机小写字母和数字",
     *     zh-CN:description="利用本方法可以生成随机数小写字母。",
     *     zh-CN:note="支持位数和指定字符范围",
     * )
     */
    public function testRandAlphaNumLowercase(): void
    {
        $this->assertSame('', Str::randAlphaNumLowercase(0));

        $this->assertTrue(
            1 === preg_match('/^[a-z0-9]+$/', Str::randAlphaNumLowercase(4))
        );

        $this->assertTrue(
            1 === preg_match('/^[a-z0-9]+$/', Str::randAlphaNumLowercase(4, 'cdefghigk2450'))
        );

        $this->assertTrue(
            4 === strlen(Str::randAlphaNumLowercase(4))
        );
    }

    /**
     * @api(
     *     zh-CN:title="随机大写字母和数字",
     *     zh-CN:description="利用本方法可以生成随机数大写字母。",
     *     zh-CN:note="支持位数和指定字符范围",
     * )
     */
    public function testRandAlphaNumUppercase(): void
    {
        $this->assertSame('', Str::randAlphaNumUppercase(0));

        $this->assertTrue(
            1 === preg_match('/^[A-Z0-9]+$/', Str::randAlphaNumUppercase(4))
        );

        $this->assertTrue(
            1 === preg_match('/^[A-Z0-9]+$/', Str::randAlphaNumUppercase(4, 'ABCDEFG1245'))
        );

        $this->assertTrue(
            4 === strlen(Str::randAlphaNumUppercase(4))
        );
    }

    /**
     * @api(
     *     zh-CN:title="随机字母",
     *     zh-CN:description="利用本方法可以生成随机字母。",
     *     zh-CN:note="支持位数和指定字符范围",
     * )
     */
    public function testRandAlpha(): void
    {
        $this->assertSame('', Str::randAlpha(0));

        $this->assertTrue(
            1 === preg_match('/^[A-Za-z]+$/', Str::randAlpha(4))
        );

        $this->assertTrue(
            1 === preg_match('/^[A-Za-z]+$/', Str::randAlpha(4, 'ABCDEFGefijk'))
        );

        $this->assertTrue(
            4 === strlen(Str::randAlpha(4))
        );
    }

    /**
     * @api(
     *     zh-CN:title="随机小写字母",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testRandAlphaLowercase(): void
    {
        $this->assertSame('', Str::randAlphaLowercase(0));

        $this->assertTrue(
            1 === preg_match('/^[a-z]+$/', Str::randAlphaLowercase(4))
        );

        $this->assertTrue(
            1 === preg_match('/^[a-z]+$/', Str::randAlphaLowercase(4, 'cdefghigk'))
        );

        $this->assertTrue(
            4 === strlen(Str::randAlphaLowercase(4))
        );
    }

    /**
     * @api(
     *     zh-CN:title="随机大写字母",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testRandAlphaUppercase(): void
    {
        $this->assertSame('', Str::randAlphaUppercase(0));

        $this->assertTrue(
            1 === preg_match('/^[A-Z]+$/', Str::randAlphaUppercase(4))
        );

        $this->assertTrue(
            1 === preg_match('/^[A-Z]+$/', Str::randAlphaUppercase(4, 'ABCDEFG'))
        );

        $this->assertTrue(
            4 === strlen(Str::randAlphaUppercase(4))
        );
    }

    /**
     * @api(
     *     zh-CN:title="随机数字",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testRandNum(): void
    {
        $this->assertSame('', Str::randNum(0));

        $this->assertTrue(
            1 === preg_match('/^[0-9]+$/', Str::randNum(4))
        );

        $this->assertTrue(
            1 === preg_match('/^[0-9]+$/', Str::randNum(4, '012456'))
        );

        $this->assertTrue(
            4 === strlen(Str::randNum(4))
        );
    }

    /**
     * @api(
     *     zh-CN:title="随机字中文",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testRandChinese(): void
    {
        $this->assertSame('', Str::randChinese(0));

        $this->assertTrue(
            1 === preg_match('/^[\x{4e00}-\x{9fa5}]+$/u', Str::randChinese(4))
        );

        $this->assertTrue(
            1 === preg_match('/^[\x{4e00}-\x{9fa5}]+$/u', Str::randChinese(4, '我是一个中国人'))
        );

        $this->assertTrue(
            12 === strlen(Str::randChinese(4))
        );
    }

    /**
     * @api(
     *     zh-CN:title="随机字符串",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testRandStr(): void
    {
        $this->assertSame('', Str::randStr(0, ''));
        $this->assertSame('', Str::randStr(5, ''));

        $this->assertTrue(
            4 === strlen(Str::randStr(4, 'helloworld'))
        );
    }

    /**
     * @api(
     *     zh-CN:title="日期格式化",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testFormatDate(): void
    {
        $time = time();

        $this->assertSame(date('Y-m-d H:i', $time + 600), Str::formatDate($time + 600));
        $this->assertSame(date('Y-m-d', $time + 600), Str::formatDate($time + 600, [], 'Y-m-d'));

        $this->assertSame(date('Y-m-d', $time - 1286400), Str::formatDate($time - 1286400, [], 'Y-m-d'));

        $this->assertSame('1 hours ago', Str::formatDate($time - 5040));
        $this->assertSame('1 小时之前', Str::formatDate($time - 5040, ['hours' => '小时之前']));

        $this->assertSame('4 minutes ago', Str::formatDate($time - 240));
        $this->assertSame('4 分钟之前', Str::formatDate($time - 240, ['minutes' => '分钟之前']));

        $this->assertTrue(in_array(Str::formatDate($time - 40), ['40 seconds ago', '41 seconds ago', '42 seconds ago'], true));
        $this->assertTrue(in_array(Str::formatDate($time - 40, ['seconds' => '秒之前']), ['40 秒之前', '41 秒之前', '42 秒之前'], true));
    }

    /**
     * @api(
     *     zh-CN:title="文件大小格式化",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testFormatBytes(): void
    {
        $this->assertSame('2.4G', Str::formatBytes(2573741824));
        $this->assertSame('2.4', Str::formatBytes(2573741824, false));

        $this->assertSame('4.81M', Str::formatBytes(5048576));
        $this->assertSame('4.81', Str::formatBytes(5048576, false));

        $this->assertSame('2.95K', Str::formatBytes(3024));
        $this->assertSame('2.95', Str::formatBytes(3024, false));

        $this->assertSame('100B', Str::formatBytes(100));
        $this->assertSame('100', Str::formatBytes(100, false));
    }

    /**
     * @api(
     *     zh-CN:title="下划线转驼峰",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testCamelize(): void
    {
        $this->assertSame('helloWorld', Str::camelize('helloWorld'));
        $this->assertSame('helloWorld', Str::camelize('helloWorld', '-'));
        $this->assertSame('helloWorld', Str::camelize('hello_world'));
        $this->assertSame('helloWorld', Str::camelize('hello-world', '-'));
    }

    /**
     * @api(
     *     zh-CN:title="驼峰转下划线",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testUnCamelize(): void
    {
        $this->assertSame('hello_world', Str::unCamelize('hello_world'));
        $this->assertSame('hello-world', Str::unCamelize('hello-world', '-'));
        $this->assertSame('hello_world', Str::unCamelize('helloWorld'));
        $this->assertSame('hello-world', Str::unCamelize('helloWorld', '-'));
    }

    public function testStrNotFound(): void
    {
        $this->expectException(\Error::class);
        $this->expectExceptionMessage('Call to undefined function Leevel\\Support\\Str\\not_found()');

        $this->assertTrue(Str::notFound());
    }
}
