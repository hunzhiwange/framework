<?php

declare(strict_types=1);

namespace Tests\Database\Ddd;

use Leevel\Database\Ddd\Entity;
use Leevel\Support\Collection;
use Tests\Database\DatabaseTestCase as TestCase;
use Tests\Database\Ddd\Entity\DemoConversionEntity;

/**
 * @api(
 *     zh-CN:title="实体类型转换",
 *     path="orm/conversion",
 *     zh-CN:description="
 * 实体所有的属性设置和获取都会经过 `setter` 和 `setter` 处理，每个实体都有通用的 `setter` 和 `getter`，也支持自定义 `setter` 和 `getter`。
 *
 * 我们可以通过自定义 `setter` 和 `setter` 方法实现属性类型转换。
 * ",
 * )
 */
class EntityConversionTest extends TestCase
{
    /**
     * @api(
     *     zh-CN:title="基本使用方法",
     *     zh-CN:description="
     * **fixture 定义**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Database\Ddd\EntityConversionTest::class, 'makeEntity')]}
     * ```
     *
     * **Tests\Database\Ddd\Entity\DemoConversionEntity**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Database\Ddd\Entity\DemoConversionEntity::class)]}
     * ```
     * ",
     *     zh-CN:note="",
     * )
     *
     * @dataProvider getBaseUseData
     *
     * @param string $field
     * @param mixed  $source
     * @param mixed  $prop
     * @param mixed  $conversion
     */
    public function testBaseUse($field, $source, $prop, $conversion): void
    {
        $entity = $this->makeEntity();
        $entity->withProp($field, $source);

        $assertMethod = in_array($field, [
            'obj1', 'obj2',
            'obj3', 'obj4',
            'coll1', 'coll2',
            'collection1', 'collection2',
        ], true) ? 'assertEquals' : 'assertSame';

        $this->assertSame($prop, $this->getTestProperty($entity, 'data')[$field]);
        $this->{$assertMethod}($conversion, $entity->prop($field));
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

    public function testInvalidSetter(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Return type of entity setter must be instance of Tests\\Database\\Ddd\\Entity\\DemoConversionEntity.');

        $entity = $this->makeEntity();
        $entity->withProp('invalid_setter', 1);
    }

    protected function makeEntity(): DemoConversionEntity
    {
        $entity = new DemoConversionEntity();
        $this->assertInstanceof(Entity::class, $entity);

        return $entity;
    }
}
