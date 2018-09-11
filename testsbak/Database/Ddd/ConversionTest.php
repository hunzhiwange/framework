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

namespace Tests\Database\Ddd;

use Carbon\Carbon;
use DateTime;
use DateTimeZone;
use Leevel\Collection\Collection;
use Leevel\Database\Ddd\Entity;
use Tests\Database\Ddd\Entity\TestConversionEntity;
use Tests\TestCase;

/**
 * conversion test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.07.02
 *
 * @version 1.0
 */
class ConversionTest extends TestCase
{
    /**
     * @dataProvider getBaseUseData
     *
     * @param string $field
     * @param mixed  $source
     * @param mixed  $prop
     * @param mixed  $conversion
     * @param mixed  $msg
     */
    public function testBaseUse($field, $source, $prop, $conversion)
    {
        $entity = $this->makeEntity();

        $old = date_default_timezone_get();
        date_default_timezone_set('UTC');

        $entity->{$field} = $source;

        $assertMethod = in_array($field, [
            'obj1', 'object1',
            'obj2', 'object2',
            'collection1', 'collection2',
            'coll1', 'coll2',
            'date1', 'date2',
            'date3', 'date4',
            'date5', 'datetime1',
            'datetime2', 'datetime3',
            'datetime4', 'datetime5',
            'time1', 'time2',
            'time3', 'time4',
            'time5', 'timestamp1',
            'timestamp2', 'timestamp3',
            'timestamp4', 'timestamp5',
        ], true) ? 'assertEquals' : 'assertSame';

        $this->assertSame($prop, $this->getTestProperty($entity, $field));
        $this->{$assertMethod}($conversion, $entity->getProp($field));

        date_default_timezone_set($old);
    }

    public function getBaseUseData()
    {
        return [
            // int = integer
            ['int1', '4.4', '4.4', 4],
            ['integer1', '55 name', '55 name', 55],

            // real = float = double
            ['real1', '55.02 xx', '55.02 xx', 55.02],
            ['float1', '55 ', '55 ', 55.0],

            // string = str
            ['double1', '0.11', '0.11', 0.11],
            [
                'string1',
                $stringClass = new class('string1') {
                    private $value;

                    public function __construct($value)
                    {
                        $this->value = $value;
                    }

                    public function __toString()
                    {
                        return 'Object to string by __toString():'.$this->value;
                    }
                },
                $stringClass,
                'Object to string by __toString():string1',
            ],
            ['str1', 4.00000, 4.00000, '4'],

            // bool = boolean
            ['bool1', 'google', 'google', true],
            ['boolean1', '', '', false],

            // obj = object
            [
                'obj1',
                ['hello', 'world'],
                '{"0":"hello","1":"world"}',
                json_decode('{"0":"hello","1":"world"}'),
            ],
            [
                'obj2',
                '{"hello":"world"}',
                '{"hello":"world"}',
                json_decode('{"hello":"world"}'),
            ],
            [
                'object1',
                ['hello', 'world'],
                '{"0":"hello","1":"world"}',
                json_decode('{"0":"hello","1":"world"}'),
            ],
            [
                'object2',
                ['hello', 'world'],
                '{"0":"hello","1":"world"}',
                json_decode('{"0":"hello","1":"world"}'),
            ],

            // arr = array = json
            ['arr1', ['foo', 'bar'], '["foo","bar"]', ['foo', 'bar']],
            ['arr2', '{"yes":"hello"}', '{"yes":"hello"}', ['yes' => 'hello']],
            ['array1', ['goods', 'name'], '["goods","name"]', ['goods', 'name']],
            ['array2', '{"goods":"name"}', '{"goods":"name"}', ['goods' => 'name']],
            ['json1', ['hello', 'world'], '["hello","world"]', ['hello', 'world']],
            ['json2', '{"foo2":"bar2"}', '{"foo2":"bar2"}', ['foo2' => 'bar2']],

            // coll = collection
            ['coll1', '{"foo2":"bar2"}', '{"foo2":"bar2"}', new Collection(['foo2' => 'bar2'])],
            ['coll2', ['foo2' => 'bar2'], '{"foo2":"bar2"}', new Collection(['foo2' => 'bar2'])],
            ['collection1', '{"foo2":"bar2"}', '{"foo2":"bar2"}', new Collection(['foo2' => 'bar2'])],
            ['collection2', ['foo2' => 'bar2'], '{"foo2":"bar2"}', new Collection(['foo2' => 'bar2'])],

            // date = datetime
            ['date1', $date1 = Carbon::create(2012, 1, 1, 0, 0, 0, 'UTC'), (string) $date1, $date1],
            ['date2', $date2 = new DateTime('2009-10-11', new DateTimeZone('UTC')), '2009-10-11 00:00:00', $date2],
            ['date3', 35, '1970-01-01 00:00:35', Carbon::create(1970, 1, 1, 0, 0, 35, 'UTC')],
            ['date4', '2018-01-11', '2018-01-11 00:00:00', Carbon::create(2018, 1, 11, 0, 0, 0, 'UTC')],
            ['date5', '2019-02-01 03:45:27', '2019-02-01 03:45:27', Carbon::create(2019, 2, 1, 3, 45, 27, 'UTC')],
            ['datetime1', $date1 = Carbon::create(2012, 1, 1, 0, 0, 0, 'UTC'), (string) $date1, $date1],
            ['datetime2', $date2 = new DateTime('2009-10-11', new DateTimeZone('UTC')), '2009-10-11 00:00:00', $date2],
            ['datetime3', 35, '1970-01-01 00:00:35', Carbon::create(1970, 1, 1, 0, 0, 35, 'UTC')],
            ['datetime4', '2018-01-11', '2018-01-11 00:00:00', Carbon::create(2018, 1, 11, 0, 0, 0, 'UTC')],
            ['datetime5', '2019-02-01 03:45:27', '2019-02-01 03:45:27', Carbon::create(2019, 2, 1, 3, 45, 27, 'UTC')],

            // time = timetamp
            ['time1', $date1 = Carbon::create(2012, 1, 1, 0, 0, 0, 'UTC'), '2012-01-01 00:00:00', $date1->getTimestamp()],
            ['time2', $date2 = new DateTime('2009-10-11', new DateTimeZone('UTC')), '2009-10-11 00:00:00', $date2->getTimestamp()],
            ['time3', 35, '1970-01-01 00:00:35', Carbon::create(1970, 1, 1, 0, 0, 35, 'UTC')->getTimestamp()],
            ['time4', '2018-01-11', '2018-01-11 00:00:00', Carbon::create(2018, 1, 11, 0, 0, 0, 'UTC')->getTimestamp()],
            ['time5', '2019-02-01 03:45:27', '2019-02-01 03:45:27', Carbon::create(2019, 2, 1, 3, 45, 27, 'UTC')->getTimestamp()],
            ['timestamp1', $date1 = Carbon::create(2012, 1, 1, 0, 0, 0, 'UTC'), '2012-01-01 00:00:00', $date1->getTimestamp()],
            ['timestamp2', $date2 = new DateTime('2009-10-11', new DateTimeZone('UTC')), '2009-10-11 00:00:00', $date2->getTimestamp()],
            ['timestamp3', 35, '1970-01-01 00:00:35', Carbon::create(1970, 1, 1, 0, 0, 35, 'UTC')->getTimestamp()],
            ['timestamp4', '2018-01-11', '2018-01-11 00:00:00', Carbon::create(2018, 1, 11, 0, 0, 0, 'UTC')->getTimestamp()],
            ['timestamp5', '2019-02-01 03:45:27', '2019-02-01 03:45:27', Carbon::create(2019, 2, 1, 3, 45, 27, 'UTC')->getTimestamp()],
        ];
    }

    protected function makeEntity()
    {
        $entity = new TestConversionEntity();

        $this->assertInstanceof(Entity::class, $entity);

        return $entity;
    }
}
