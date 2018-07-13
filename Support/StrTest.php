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

    public function testRandString()
    {
        $this->assertSame('', Str::randSting(0, ''));

        $this->assertSame('', Str::randSting(5, ''));

        $this->assertTrue(
            4 === strlen(Str::randSting(4, 'helloworld'))
        );
    }
}
