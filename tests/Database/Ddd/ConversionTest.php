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
 * (c) 2010-2019 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Database\Ddd;

use Leevel\Collection\Collection;
use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\IEntity;
use Tests\Database\DatabaseTestCase as TestCase;
use Tests\Database\Ddd\Entity\TestConversionEntity;

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

        $entity->__set($field, $source);

        $assertMethod = in_array($field, [
            'obj1', 'obj2',
            'obj3', 'obj4',
            'coll1', 'coll2',
            'collection1', 'collection2',
        ], true) ? 'assertEquals' : 'assertSame';

        $this->assertSame($prop, $this->getTestProperty($entity, $field));
        $this->{$assertMethod}($conversion, $entity->__get($field));
    }

    public function getBaseUseData()
    {
        return [
            // int
            ['int1', '4.4', 4, 5],
            ['int2', 55, 55, 55],

            // float
            ['float1', '55.02 xx', 55.02, 56.02],
            ['float2', 55.02, 55.02, 55.02],
            ['float3', 55, 55.0, 55.0],

            // string
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
                'Object to string by __toString():string1',
                'Object to string by __toString():string1',
            ],
            ['string2', 'foo', 'foo', 'foo'],

            ['bool1', 'google', true, true],
            ['bool2', '', false, false],
            ['bool3', true, true, true],
            ['bool4', false, false, false],

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
                'obj3',
                (object) ['foo' => 'bar', 'hello' => 'world'],
                '{"foo":"bar","hello":"world"}',
                json_decode('{"foo":"bar","hello":"world"}'),
            ],
            ['arr1', ['foo', 'bar'], '["foo","bar"]', ['foo', 'bar']],
            ['arr2', '{"yes":"hello"}', '{"yes":"hello"}', ['yes' => 'hello']],
            ['json1', ['hello', 'world'], '["hello","world"]', ['hello', 'world']],
            ['json2', '{"foo2":"bar2"}', '{"foo2":"bar2"}', ['foo2' => 'bar2']],

            ['coll1', '{"foo2":"bar2"}', '{"foo2":"bar2"}', new Collection(['foo2' => 'bar2'])],
            ['coll2', ['foo2' => 'bar2'], '{"foo2":"bar2"}', new Collection(['foo2' => 'bar2'])],
        ];
    }

    protected function makeEntity(): TestConversionEntity
    {
        $entity = new TestConversionEntity();

        $this->assertInstanceof(Entity::class, $entity);
        $this->assertInstanceof(IEntity::class, $entity);

        return $entity;
    }
}
