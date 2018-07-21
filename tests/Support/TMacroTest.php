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

use Leevel\Support\TMacro;
use Tests\TestCase;

/**
 * tMacro test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.07.21
 *
 * @version 1.0
 */
class TMacroTest extends TestCase
{
    public function testBaseUse()
    {
        $test = new MacroTest1();

        $test->macro('hello', function() {
            return 'world';
        });

        $this->assertSame('world', $test->hello());
        $this->assertSame('world', MacroTest1::hello());

        $test->macro('world', function() {
            return $this->hello.'-'.$this::$world;
        });

        $this->assertSame('queryphp-static world', $test->world());

        $test->macro('test', [$this, 'md5']);

        $this->assertSame('827ccb0eea8a706c4c34a16891f84e7b', $test->test(12345));
    }

    public function testBadMethodCallException()
    {
        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage(
            'Method worldNotFound is not exits.'
        );

        $test = new MacroTest1();

        $test->worldNotFound();
    }

    public function testStaticBadMethodCallException()
    {
        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage(
            'Method worldNotFound is not exits.'
        );

        MacroTest1::worldNotFound();
    }

    public function md5(string $key): string
    {
        return md5($key);
    }
}

class MacroTest1
{
    use TMacro;

    public static $world = 'static world';

    public $hello = 'queryphp';
}
