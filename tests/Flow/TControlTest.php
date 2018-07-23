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

namespace Tests\Flow;

use Leevel\Flow\TControl;
use Tests\TestCase;

/**
 * tControl test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.07.23
 *
 * @version 1.0
 */
class TControlTest extends TestCase
{
    public function testBaseUse()
    {
        $test = new Test1();

        $this->assertSame('', $test->value());

        $condition1 = 1;

        $value = $test->

        ifs($condition1)->condition1()->

        elses()->condition2()->

        endIfs()->

        value();

        $this->assertSame('condition1', $value);
    }

    public function testElses()
    {
        $test = new Test1();

        $condition1 = 0;

        $value = $test->

        ifs($condition1)->condition1()->

        elses()->condition2()->

        endIfs()->

        value();

        $this->assertSame('condition2', $value);
    }

    /**
     * @dataProvider getElsesData
     *
     * @param int    $condition
     * @param string $result
     */
    public function testElse2(int $condition, string $result)
    {
        $test = new Test1();

        $value = $test->

        ifs(0 === $condition)->condition1()->

        elseIfs(1 === $condition)->condition2()->

        elseIfs(2 === $condition)->condition3()->

        elseIfs(3 === $condition)->condition4()->

        // elses 仅能根据上一次的 elseIfs 或 ifs 来做判断，这里为 elseIfs(3 === $condition)
        elses()->condition5()->

        endIfs()->

        value();

        $this->assertSame($result, $value);
    }

    public function getElsesData()
    {
        return [
            [0, 'condition1 condition5'],
            [1, 'condition2 condition5'],
            [2, 'condition3 condition5'],
            [3, 'condition4'], // elses 仅能根据上一次的 elseIfs 或 ifs 来做判断，这里为 elseIfs(3 === $condition)
            [4, 'condition5'],
            [5, 'condition5'],
            [6, 'condition5'],
        ];
    }

    public function testElseIfs()
    {
        $test = new Test1();

        $condition1 = 1;

        $value = $test->

        ifs(0 === $condition1)->condition1()->

        elseIfs(1 === $condition1)->condition2()->

        endIfs()->

        value();

        $this->assertSame('condition2', $value);
    }

    public function testPlaceholderTControl()
    {
        $test = new Test1();

        $this->assertInstanceof(Test1::class, $test->placeholder());
        $this->assertInstanceof(Test1::class, $test->foobar());
    }
}

class Test1
{
    use TControl;

    protected $value = [];

    public function __call(string $method, array $args)
    {
        if ($this->placeholderTControl($method)) {
            return $this;
        }
    }

    public function value()
    {
        return implode(' ', $this->value);
    }

    public function condition1()
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $this->value[] = 'condition1';

        return $this;
    }

    public function condition2()
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $this->value[] = 'condition2';

        return $this;
    }

    public function condition3()
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $this->value[] = 'condition3';

        return $this;
    }

    public function condition4()
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $this->value[] = 'condition4';

        return $this;
    }

    public function condition5()
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $this->value[] = 'condition5';

        return $this;
    }
}
