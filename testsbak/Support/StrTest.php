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

namespace Tests\Support;

use Leevel\Support\Str;
use Tests\TestCase;

/**
 * str test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.07.12
 *
 * @version 1.0
 */
class StrTest extends TestCase
{
    public function testBaseUse()
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

    public function testRandAlphaNumLowercase()
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

    public function testRandAlphaNumUppercase()
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

    public function testRandAlpha()
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

    public function testRandAlphaLowercase()
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

    public function testRandAlphaUppercase()
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

    public function testRandNum()
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

    public function testRandChinese()
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

    public function testRandStr()
    {
        $this->assertSame('', Str::randStr(0, ''));

        $this->assertSame('', Str::randStr(5, ''));

        $this->assertTrue(
            4 === strlen(Str::randStr(4, 'helloworld'))
        );
    }

    public function testStrEncoding()
    {
        $this->assertSame('hello', Str::StrEncoding('hello', 'gbk'));

        $this->assertSame(['key' => 'hello'], Str::StrEncoding(['key' => 'hello'], 'gbk'));

        $this->assertSame('hello', Str::StrEncoding('hello', 'gbk', 'gbk'));
    }

    public function testSubstr()
    {
        $this->assertSame('我', Str::substr('我是人', 0, 1));
        $this->assertSame('我是', Str::substr('我是人', 0, 2));
        $this->assertSame('人', Str::substr('我是人', 2));
    }

    public function testFormatDate()
    {
        $time = time();

        $this->assertSame(date('Y-m-d H:i', $time + 600), Str::formatDate($time + 600));
        $this->assertSame(date('Y-m-d', $time + 600), Str::formatDate($time + 600, [], 'Y-m-d'));

        $this->assertSame(date('Y-m-d', $time - 1286400), Str::formatDate($time - 1286400, [], 'Y-m-d'));

        $this->assertSame('1 hours ago', Str::formatDate($time - 5040));
        $this->assertSame('1 小时之前', Str::formatDate($time - 5040, ['hours' => '小时之前']));

        $this->assertSame('4 minutes ago', Str::formatDate($time - 240));
        $this->assertSame('4 分钟之前', Str::formatDate($time - 240, ['minutes' => '分钟之前']));

        $this->assertSame('40 seconds ago', Str::formatDate($time - 40));
        $this->assertSame('40 秒之前', Str::formatDate($time - 40, ['seconds' => '秒之前']));
    }

    public function testFormatBytes()
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

    public function testCamelize()
    {
        $this->assertSame('helloworld', Str::camelize('helloWorld'));

        $this->assertSame('helloworld', Str::camelize('helloWorld', '-'));

        $this->assertSame('helloWorld', Str::camelize('hello_world'));

        $this->assertSame('helloWorld', Str::camelize('hello-world', '-'));
    }

    public function testUnCamelize()
    {
        $this->assertSame('hello_world', Str::unCamelize('hello_world'));

        $this->assertSame('hello-world', Str::unCamelize('hello-world', '-'));

        $this->assertSame('hello_world', Str::unCamelize('helloWorld'));

        $this->assertSame('hello-world', Str::unCamelize('helloWorld', '-'));
    }

    public function testStartsWith()
    {
        $this->assertFalse(Str::startsWith('foo', 'hello'));

        $this->assertTrue(Str::startsWith('foo bar', 'foo'));
    }

    public function testEndsWith()
    {
        $this->assertFalse(Str::endsWith('foo', 'hello'));

        $this->assertTrue(Str::endsWith('foo bar', 'bar'));
    }

    public function testContains()
    {
        $this->assertFalse(Str::contains('foo', ''));

        $this->assertTrue(Str::contains('foo bar', 'foo'));
    }
}
