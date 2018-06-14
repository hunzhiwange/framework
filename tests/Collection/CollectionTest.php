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

namespace Tests\Collection;

use Leevel\Collection\Collection;
use Tests\TestCase;

/**
 * collection test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.06.03
 *
 * @version 1.0
 * @coversNothing
 */
class CollectionTest extends TestCase
{
    public function testBaseUse()
    {
        $data = [
            'hello', 'world', 'foo', 'bar',
        ];

        $collection = new Collection($data);

        foreach ($collection as $key => $val) {
            switch ($key) {
                case 0:
                    $this->assertSame($val, 'hello');

                    break;
                case 1:
                    $this->assertSame($val, 'world');

                    break;
                case 2:
                    $this->assertSame($val, 'foo');

                    break;
                case 3:
                    $this->assertSame($val, 'bar');

                    break;
            }
        }
    }

    public function testIterator()
    {
        $data = [
            'hello', 'world', 'foo', 'bar',
        ];

        $collection = new Collection($data);

        $this->assertSame('hello', $collection->current());
        $this->assertSame(0, $collection->key());

        $this->assertNull($collection->next());

        $this->assertSame('world', $collection->current());
        $this->assertSame(1, $collection->key());

        $this->assertNull($collection->next());
        $this->assertNull($collection->next());

        $this->assertSame('bar', $collection->current());
        $this->assertSame(3, $collection->key());

        $collection->next();

        $this->assertFalse($collection->current());
        $this->assertNull($collection->key());

        $collection->rewind();
        $this->assertSame(0, $collection->key());
        $this->assertSame('hello', $collection->current());
    }

    public function testCountable()
    {
        $data = [
            'hello', 'world', 'foo', 'bar',
        ];

        $collection = new Collection($data);

        $this->assertSame(4, count($collection));
    }

    public function testArrayAccess()
    {
        $data = [
            'hello',
        ];

        $collection = new Collection($data);

        $this->assertSame($collection->toArray(), $data);

        $collection[1] = 'world';
        $data[1] = 'world';
        $this->assertSame($collection->toArray(), $data);

        $collection[2] = 'foo';
        $data[2] = 'foo';
        $this->assertSame($collection->toArray(), $data);

        unset($collection[1]);

        $this->assertSame($collection->toArray(), [
            0 => 'hello',
            2 => 'foo',
        ]);
    }
}
