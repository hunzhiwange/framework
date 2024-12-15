<?php

declare(strict_types=1);

namespace Tests\Database\Ddd;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\EntityCollection as Collection;
use Leevel\Kernel\Utils\Api;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Database\DatabaseTestCase as TestCase;
use Tests\Database\Ddd\Entity\DemoConversionEntity;
use Tests\Database\Ddd\Entity\DemoEntity;

#[Api([
    'zh-CN:title' => '实体类型转换',
    'path' => 'orm/conversion',
    'zh-CN:description' => <<<'EOT'
实体所有的属性设置和获取都会经过 `getter` 和 `setter` 处理，每个实体都有通用的 `getter` 和 `setter`，也支持自定义 `getter` 和 `setter`。

我们可以通过属性钩子实现属性类型转换。
EOT,
])]
final class EntityConversionTest extends TestCase
{
    #[DataProvider('getBaseUseData')]
    #[Api([
        'zh-CN:title' => '基本使用方法',
        'zh-CN:description' => <<<'EOT'
**fixture 定义**

``` php
{[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Database\Ddd\EntityConversionTest::class, 'makeEntity')]}
```

**Tests\Database\Ddd\Entity\DemoConversionEntity**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Database\Ddd\Entity\DemoConversionEntity::class)]}
```
EOT,
    ])]
    public function testBaseUse($field, $source, $prop, $conversion): void
    {
        $entity = $this->makeEntity();
        $entity->setProp($field, $source);
        $assertMethod = \in_array($field, [
            'obj1', 'obj2',
            'obj3', 'obj4',
            'coll1', 'coll2',
            'collection1', 'collection2',
        ], true) ? 'assertEquals' : 'assertSame';
        $this->{$assertMethod}($prop, $entity->{$field});
        $this->{$assertMethod}($conversion, $entity->getProp($field));
    }

    public static function getBaseUseData(): array
    {
        return [
            // int
            ['int1', '4.4', 5, 4],
            ['int2', 55, 55, 55],

            // float
            ['float1', '55.02 poolTranscation', 56.02, 55.02],
            ['float2', 55.02, 55.02, 55.02],
            ['float3', 55, 55.0, 55.0],

            // string
            [
                'string1',
                new class('string1') {
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

            // bool
            ['bool1', 'google', true, true],
            ['bool2', '', false, false],
            ['bool3', true, true, true],
            ['bool4', false, false, false],
            [
                'obj1',
                ['hello', 'world'],
                json_decode('{"0":"hello","1":"world"}'),
                '{"0":"hello","1":"world"}',
            ],
            [
                'obj2',
                '{"hello":"world"}',
                json_decode('{"hello":"world"}'),
                '{"hello":"world"}',
            ],
            [
                'obj3',
                '{"foo":"bar","hello":"world"}',
                '{"foo":"bar","hello":"world"}',
                '"{\"foo\":\"bar\",\"hello\":\"world\"}"',
            ],
            ['arr1', ['foo', 'bar'], ['foo', 'bar'], '["foo","bar"]'],
            ['arr2', '{"yes":"hello"}', ['yes' => 'hello'], '{"yes":"hello"}'],
            ['json1', ['hello', 'world'], ['hello', 'world'], '["hello","world"]'],
            ['json2', '[{"foo2":"bar2"}]', [['foo2' => 'bar2']], '[{"foo2":"bar2"}]'],
            ['coll1', '{"name":"bar2"}', new Collection([new DemoEntity(['name' => 'bar2'])]), '{"name":"bar2"}'],
            ['coll2', ['name' => 'bar2'], new Collection([new DemoEntity(['name' => 'bar2'])]), '{"name":"bar2"}'],
        ];
    }

    protected function makeEntity(): DemoConversionEntity
    {
        $entity = new DemoConversionEntity();
        $this->assertInstanceof(Entity::class, $entity);

        return $entity;
    }
}
