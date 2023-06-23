<?php

declare(strict_types=1);

namespace Tests\Support;

use Leevel\Support\FlowControl;
use Tests\TestCase;

/**
 * @api(
 *     zh-CN:title="Flow Control",
 *     zh-CN:title="流程控制",
 *     path="component/flow",
 *     zh-CN:description="
 * QueryPHP 为流程控制类统一抽象了一个基础流程控制类 `\Leevel\Support\FlowControl`，流程控制类可以轻松接入。
 *
 * 系统一些关键服务，比如说数据库查询条件、HTTP 响应等流程控制类均接入了统一的抽象层。
 * ",
 * note="你可以根据不同场景灵活运用，以满足产品需求。",
 * )
 *
 * @internal
 */
final class FlowControlTest extends TestCase
{
    /**
     * @api(
     *     zh-CN:title="基础使用方法",
     *     zh-CN:description="
     * **fixture 定义**
     *
     * **Tests\Flow\Test1**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Support\FlowTest1::class)]}
     * ```
     * ",
     *     zh-CN:note="",
     * )
     */
    public function testBaseUse(): void
    {
        $test = new FlowTest1();

        static::assertSame('', $test->value());

        $condition1 = 1;

        $value = $test
            ->if($condition1)
            ->condition1()
            ->else()
            ->condition2()
            ->fi()
            ->value()
        ;

        static::assertSame('condition1', $value);
    }

    /**
     * @api(
     *     zh-CN:title="else 条件语句",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testElse(): void
    {
        $test = new FlowTest1();

        $condition1 = 0;

        $value = $test
            ->if($condition1)
            ->condition1()
            ->else()
            ->condition2()
            ->fi()
            ->value()
        ;

        static::assertSame('condition2', $value);
    }

    /**
     * @dataProvider getElseData
     *
     * @api(
     *     zh-CN:title="else 条件语句例子",
     *     zh-CN:description="
     * **测试例子**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Support\FlowControlTest::class, 'getElseData')]}
     * ```
     * ",
     *     zh-CN:note="",
     * )
     */
    public function testElseMulti(int $condition, string $result): void
    {
        $test = new FlowTest1();

        $value = $test
            ->if(0 === $condition)
            ->condition1()
            ->elif(1 === $condition)
            ->condition2()
            ->elif(2 === $condition)
            ->condition3()
            ->elif(3 === $condition)
            ->condition4()
            ->else()
            ->condition5()
            ->fi()
            ->value()
        ;

        static::assertSame($result, $value);
    }

    public static function getElseData()
    {
        return [
            [0, 'condition1'],
            [1, 'condition2'],
            [2, 'condition3'],
            [3, 'condition4'],
            [4, 'condition5'],
            [5, 'condition5'],
            [6, 'condition5'],
        ];
    }

    /**
     * @api(
     *     zh-CN:title="elif 条件语句",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testElseIfs(): void
    {
        $test = new FlowTest1();

        $condition1 = 1;

        $value = $test
            ->if(0 === $condition1)
            ->condition1()
            ->elif(1 === $condition1)
            ->condition2()
            ->fi()
            ->value()
        ;

        static::assertSame('condition2', $value);
    }

    /**
     * @api(
     *     zh-CN:title="条件语句支持嵌套",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testNested(): void
    {
        $queryBuilder = new QueryBuilderFlowControl();

        $condition = 0;
        $anotherCondition = 'bar';
        $anotherConditionSub = 'bar';

        $value = $queryBuilder
            ->if(0 === $condition)
            ->where('id', 0)
            ->if('foo' === $anotherCondition)
            ->where('id', 11)
            ->elif('bar' === $anotherCondition)
            ->where('id', 22)
            ->else()
            ->where('id', 33)
            ->if('1' === $anotherConditionSub)
            ->where('id', 111)
            ->elif('2' === $anotherConditionSub)
            ->where('id', 222)
            ->else()
            ->where('id', 333)
            ->fi()
            ->fi()
            ->elif(1 === $condition)
            ->where('id', 4)
            ->else()
            ->where('id', 5)
            ->fi()
            ->getQuery()
        ;

        $data = <<<'eot'
[
    [
        "id",
        0
    ],
    [
        "id",
        22
    ]
]
eot;

        static::assertSame(
            $data,
            $this->varJson(
                $value
            )
        );
    }

    public function testNested2(): void
    {
        $queryBuilder = new QueryBuilderFlowControl();

        $condition = 0;
        $anotherCondition = 'foo';
        $anotherConditionSub = 'bar';

        $value = $queryBuilder
            ->if(0 === $condition)
            ->where('id', 0)
            ->if('foo' === $anotherCondition)
            ->where('id', 11)
            ->elif('bar' === $anotherCondition)
            ->where('id', 22)
            ->else()
            ->where('id', 33)
            ->if('1' === $anotherConditionSub)
            ->where('id', 111)
            ->elif('2' === $anotherConditionSub)
            ->where('id', 222)
            ->else()
            ->where('id', 333)
            ->fi()
            ->fi()
            ->elif(1 === $condition)
            ->where('id', 4)
            ->else()
            ->where('id', 5)
            ->fi()
            ->getQuery()
        ;

        $data = <<<'eot'
[
    [
        "id",
        0
    ],
    [
        "id",
        11
    ]
]
eot;

        static::assertSame(
            $data,
            $this->varJson(
                $value
            )
        );
    }

    public function testNested3(): void
    {
        $queryBuilder = new QueryBuilderFlowControl();

        $condition = 0;
        $anotherCondition = 'foo_not_found';
        $anotherConditionSub = 'bar';

        $value = $queryBuilder
            ->if(0 === $condition)
            ->where('id', 0)
            ->if('foo' === $anotherCondition)
            ->where('id', 11)
            ->elif('bar' === $anotherCondition)
            ->where('id', 22)
            ->else()
            ->where('id', 33)
            ->if('1' === $anotherConditionSub)
            ->where('id', 111)
            ->elif('2' === $anotherConditionSub)
            ->where('id', 222)
            ->else()
            ->where('id', 333)
            ->fi()
            ->fi()
            ->elif(1 === $condition)
            ->where('id', 4)
            ->else()
            ->where('id', 5)
            ->fi()
            ->getQuery()
        ;

        $data = <<<'eot'
[
    [
        "id",
        0
    ],
    [
        "id",
        33
    ],
    [
        "id",
        333
    ]
]
eot;

        static::assertSame(
            $data,
            $this->varJson(
                $value
            )
        );
    }

    public function testNested4(): void
    {
        $queryBuilder = new QueryBuilderFlowControl();

        $condition = 9999;
        $anotherCondition = 'foo_not_found';
        $anotherConditionSub = 'bar';

        $value = $queryBuilder
            ->if(0 === $condition)
            ->where('id', 0)
            ->if('foo' === $anotherCondition)
            ->where('id', 11)
            ->elif('bar' === $anotherCondition)
            ->where('id', 22)
            ->else()
            ->where('id', 33)
            ->if('1' === $anotherConditionSub)
            ->where('id', 111)
            ->elif('2' === $anotherConditionSub)
            ->where('id', 222)
            ->else()
            ->where('id', 333)
            ->fi()
            ->fi()
            ->elif(1 === $condition)
            ->where('id', 4)
            ->else()
            ->where('id', 5)
            ->fi()
            ->getQuery()
        ;

        $data = <<<'eot'
[
    [
        "id",
        5
    ]
]
eot;

        static::assertSame(
            $data,
            $this->varJson(
                $value
            )
        );
    }

    public function testNested5(): void
    {
        $queryBuilder = new QueryBuilderFlowControl();

        $condition = 1;
        $anotherCondition = 'foo_not_found';
        $anotherConditionSub = 'bar';

        $value = $queryBuilder
            ->if(0 === $condition)
            ->where('id', 0)
            ->if('foo' === $anotherCondition)
            ->where('id', 11)
            ->elif('bar' === $anotherCondition)
            ->where('id', 22)
            ->else()
            ->where('id', 33)
            ->if('1' === $anotherConditionSub)
            ->where('id', 111)
            ->elif('2' === $anotherConditionSub)
            ->where('id', 222)
            ->else()
            ->where('id', 333)
            ->fi()
            ->fi()
            ->elif(1 === $condition)
            ->where('id', 4)
            ->else()
            ->where('id', 5)
            ->fi()
            ->getQuery()
        ;

        $data = <<<'eot'
[
    [
        "id",
        4
    ]
]
eot;

        static::assertSame(
            $data,
            $this->varJson(
                $value
            )
        );
    }
}

class FlowTest1
{
    use FlowControl;

    protected $value = [];

    public function __call(string $method, array $args): void
    {
    }

    public function value()
    {
        return implode(' ', $this->value);
    }

    public function condition1()
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        $this->value[] = 'condition1';

        return $this;
    }

    public function condition2()
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        $this->value[] = 'condition2';

        return $this;
    }

    public function condition3()
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        $this->value[] = 'condition3';

        return $this;
    }

    public function condition4()
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        $this->value[] = 'condition4';

        return $this;
    }

    public function condition5()
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        $this->value[] = 'condition5';

        return $this;
    }
}

class QueryBuilderFlowControl
{
    use FlowControl;

    protected array $query = [];

    public function where(string $column, int $value): static
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        $this->query[] = [$column, $value];

        return $this;
    }

    public function getQuery(): array
    {
        return $this->query;
    }
}
