<?php

declare(strict_types=1);

namespace Tests\Support;

use Leevel\Kernel\Utils\Api;
use Leevel\Support\Arr\ConvertJson;
use Leevel\Support\Arr\Except;
use Leevel\Support\Arr\Filter;
use Leevel\Support\Arr\InCondition;
use Leevel\Support\Arr\Normalize;
use Leevel\Support\Arr\Only;
use Leevel\Support\Arr\ShouldJson;
use Leevel\Support\IArray;
use Leevel\Support\IJson;
use Tests\TestCase;

#[Api([
    'zh-CN:title' => '数组',
    'path' => 'component/support/arr',
    'zh-CN:description' => <<<'EOT'
这里为系统提供的数组使用的功能文档说明。
EOT,
])]
final class ArrTest extends TestCase
{
    #[Api([
        'zh-CN:title' => 'normalize 基础格式化',
    ])]
    public function testBaseUse(): void
    {
        self::assertTrue(Normalize::handle(true));
        self::assertSame(['a', 'b'], Normalize::handle('a,b'));
        self::assertSame(['a', 'b'], Normalize::handle(['a', 'b']));
        self::assertSame(['a'], Normalize::handle(['a', '']));
        self::assertSame(['a', ''], Normalize::handle(['a', ''], ',', ['']));
        self::assertSame(['a', '0'], Normalize::handle(['a', ' 0 '], ',', ['0']));
        self::assertSame(['a', '0'], Normalize::handle(['a', ' 0 '], ','));
    }

    #[Api([
        'zh-CN:title' => 'normalize 格式化字符串',
    ])]
    public function testNormalize(): void
    {
        $result = Normalize::handle('hello');

        $json = <<<'eot'
            [
                "hello"
            ]
            eot;

        self::assertSame(
            $json,
            $this->varJson(
                $result
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'normalize 格式化分隔字符串',
    ])]
    public function testNormalizeSplitString(): void
    {
        $result = Normalize::handle('hello,world');

        $json = <<<'eot'
            [
                "hello",
                "world"
            ]
            eot;

        self::assertSame(
            $json,
            $this->varJson(
                $result
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'normalize 格式化数组',
    ])]
    public function testNormalizeArr(): void
    {
        $result = Normalize::handle(['hello', 'world']);

        $json = <<<'eot'
            [
                "hello",
                "world"
            ]
            eot;

        self::assertSame(
            $json,
            $this->varJson(
                $result
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'normalize 格式化数组过滤空格',
    ])]
    public function testNormalizeArrFilterEmpty(): void
    {
        $result = Normalize::handle(['hello', 'world', ' ', '0']);

        $json = <<<'eot'
            {
                "0": "hello",
                "1": "world",
                "3": "0"
            }
            eot;

        self::assertSame(
            $json,
            $this->varJson(
                $result
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'normalize 格式化数组不过滤空格',
    ])]
    public function testNormalizeArrNotFilterEmpty(): void
    {
        $result = Normalize::handle(['hello', 'world', ' ', '0'], ',', ['']);

        $json = <<<'eot'
            [
                "hello",
                "world",
                ""
            ]
            eot;

        self::assertSame(
            $json,
            $this->varJson(
                $result
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'normalize 格式化数据即不是数组也不是字符串',
    ])]
    public function testNormalizeNotArrAndNotString(): void
    {
        $result = Normalize::handle(false);

        self::assertFalse($result);
    }

    #[Api([
        'zh-CN:title' => 'only 允许特定 Key 通过',
        'zh-CN:description' => <<<'EOT'
相当于白名单。
EOT,
    ])]
    public function testOnly(): void
    {
        $result = Only::handle(['input' => 'test', 'foo' => 'bar', 'hello' => 'world'], ['input', 'hello', 'notfound']);

        $json = <<<'eot'
            {
                "input": "test",
                "hello": "world"
            }
            eot;

        self::assertSame(
            $json,
            $this->varJson(
                $result
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'except 排除特定 Key 通过',
        'zh-CN:description' => <<<'EOT'
相当于黑名单。
EOT,
    ])]
    public function testExcept(): void
    {
        $result = Except::handle(['input' => 'test', 'foo' => 'bar', 'hello' => 'world'], ['input', 'hello', 'notfound']);

        $json = <<<'eot'
            {
                "foo": "bar"
            }
            eot;

        self::assertSame(
            $json,
            $this->varJson(
                $result
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'filter 数据过滤',
        'zh-CN:description' => <<<'EOT'
基本的字符串会执行一次清理工作。
EOT,
    ])]
    public function testFilter(): void
    {
        $sourceData = ['foo' => 'bar', 'hello' => 'world ', 'i' => '5'];
        $rule = [];

        $result = Filter::handle($sourceData, $rule);

        $json = <<<'eot'
            {
                "foo": "bar",
                "hello": "world",
                "i": "5"
            }
            eot;

        self::assertSame(
            $json,
            $this->varJson(
                $result
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'filter 数据过滤待规则',
    ])]
    public function testFilterWithRule(): void
    {
        $sourceData = ['foo' => 'bar', 'hello' => 'world ', 'i' => '5'];
        $rule = [
            'i' => ['intval'],
            'foo' => ['md5'],
            'bar' => [static function ($v) {
                return $v.' php';
            }],
        ];

        $result = Filter::handle($sourceData, $rule);

        $json = <<<'eot'
            {
                "foo": "37b51d194a7513e45b56f6524f2d51f2",
                "hello": "world",
                "i": 5
            }
            eot;

        self::assertSame(
            $json,
            $this->varJson(
                $result
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'filter 数据过滤待规则必须是数组',
    ])]
    public function testFilterRuleIsNotArr(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Rule of `i` must be an array.'
        );

        $sourceData = ['foo' => 'bar', 'hello' => 'world ', 'i' => '5'];
        $rule = [
            'i' => 'intval',
        ];

        Filter::handle($sourceData, $rule);
    }

    #[Api([
        'zh-CN:title' => 'filter 数据过滤待规则不是一个回调',
    ])]
    public function testFilterRuleItemIsNotACallback(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Rule item of `i` must be a callback type.'
        );

        $sourceData = ['foo' => 'bar', 'hello' => 'world ', 'i' => '5'];
        $rule = [
            'i' => ['notcallback'],
        ];

        Filter::handle($sourceData, $rule);
    }

    #[Api([
        'zh-CN:title' => 'filter 数据过滤默认不处理 NULL 值',
    ])]
    public function testFilterWithoutMust(): void
    {
        $sourceData = ['foo' => null];
        $rule = ['foo' => ['intval']];

        $result = Filter::handle($sourceData, $rule);

        $json = <<<'eot'
            {
                "foo": null
            }
            eot;

        self::assertSame(
            $json,
            $this->varJson(
                $result
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'filter 数据过滤强制处理 NULL 值',
    ])]
    public function testFilterWithMust(): void
    {
        $sourceData = ['foo' => null];
        $rule = ['foo' => ['intval', 'must']];

        $result = Filter::handle($sourceData, $rule);

        $json = <<<'eot'
            {
                "foo": 0
            }
            eot;

        self::assertSame(
            $json,
            $this->varJson(
                $result
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'shouldJson 数据过滤强制处理 NULL 值',
        'zh-CN:description' => <<<'EOT'
测试实现了 `\Leevel\Support\IArray` 的对象

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Support\ArrMyArray::class)]}
```

测试实现了 `\Leevel\Support\IJson` 的对象

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Support\ArrMyJson::class)]}
```

测试实现了 `\JsonSerializable` 的对象

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Support\ArrMyJsonSerializable::class)]}
```
EOT,
    ])]
    public function testShouldJson(): void
    {
        self::assertTrue(ShouldJson::handle(['foo' => 'bar']));
        self::assertTrue(ShouldJson::handle(new ArrMyArray()));
        self::assertTrue(ShouldJson::handle(new ArrMyJson()));
        self::assertTrue(ShouldJson::handle(new ArrMyJsonSerializable()));
    }

    #[Api([
        'zh-CN:title' => 'convertJson 转换 JSON 数据',
    ])]
    public function testConvertJson(): void
    {
        self::assertSame('{"foo":"bar"}', ConvertJson::handle(['foo' => 'bar']));
        self::assertSame('{"foo":"bar"}', ConvertJson::handle(['foo' => 'bar'], JSON_THROW_ON_ERROR));
        self::assertSame('{"hello":"IArray"}', ConvertJson::handle(new ArrMyArray()));
        self::assertSame('{"hello":"IJson"}', ConvertJson::handle(new ArrMyJson()));
        self::assertSame('{"hello":"JsonSerializable"}', ConvertJson::handle(new ArrMyJsonSerializable()));
        self::assertSame('{"成":"都"}', ConvertJson::handle(['成' => '都']));
        self::assertSame('{"\u6210":"\u90fd"}', ConvertJson::handle(['成' => '都'], 0));
    }

    public function testConvertJsonWithInvalidUtf8Characters(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Malformed UTF-8 characters, possibly incorrectly encoded'
        );

        ConvertJson::handle("\xB1\x31");
    }

    public function testConvertJsonWithInvalidUtf8CharactersAndThrowJsonException(): void
    {
        $this->expectException(\JsonException::class);
        $this->expectExceptionMessage(
            'Malformed UTF-8 characters, possibly incorrectly encoded'
        );

        ConvertJson::handle("\xB1\x31", JSON_THROW_ON_ERROR);
    }

    public function testConvertJsonJsonSerializeThrowException(): void
    {
        $this->expectException(ArrMyException::class);
        $this->expectExceptionMessage(
            'json exception'
        );

        ConvertJson::handle(new ArrMyJsonSerializableWithException(), JSON_THROW_ON_ERROR);
    }

    #[Api([
        'zh-CN:title' => 'inCondition 数据库 IN 查询条件',
    ])]
    public function testInCondition(): void
    {
        $data = [
            ['id' => 5, 'name' => 'hello'],
            ['id' => 6, 'name' => 'world'],
        ];

        $dataDemo2 = [
            [10, 'hello'],
            [11, 'world'],
        ];

        $result = InCondition::handle($data, 'id');
        $json = <<<'eot'
            [
                5,
                6
            ]
            eot;

        self::assertSame(
            $json,
            $this->varJson($result)
        );

        $result = InCondition::handle($data, 'name');
        $json = <<<'eot'
            [
                "hello",
                "world"
            ]
            eot;

        self::assertSame(
            $json,
            $this->varJson($result)
        );

        $result = InCondition::handle($dataDemo2, 0);
        $json = <<<'eot'
            [
                10,
                11
            ]
            eot;

        self::assertSame(
            $json,
            $this->varJson($result)
        );
    }

    public function testInConditionUnique(): void
    {
        $data = [
            ['id' => 5, 'name' => 'hello'],
            ['id' => 6, 'name' => 'world'],
            ['id' => 6, 'name' => 'world'],
        ];

        $result = InCondition::handle($data, 'id');
        $json = <<<'eot'
            [
                5,
                6
            ]
            eot;

        self::assertSame(
            $json,
            $this->varJson($result)
        );

        $result = InCondition::handle($data, 'name');
        $json = <<<'eot'
            [
                "hello",
                "world"
            ]
            eot;

        self::assertSame(
            $json,
            $this->varJson($result)
        );
    }

    public function testInConditionWithEmpty(): void
    {
        $data = [
            ['id' => 5, 'name' => ''],
            ['id' => 0, 'name' => 'world'],
            ['id' => 6, 'name' => ''],
        ];

        $result = InCondition::handle($data, 'id');
        $json = <<<'eot'
            [
                5,
                0,
                6
            ]
            eot;

        self::assertSame(
            $json,
            $this->varJson($result)
        );

        $result = InCondition::handle($data, 'name');
        $json = <<<'eot'
            [
                "",
                "world"
            ]
            eot;

        self::assertSame(
            $json,
            $this->varJson($result)
        );
    }

    #[Api([
        'zh-CN:title' => 'inCondition 数据库 IN 查询条件支持过滤器',
    ])]
    public function testInConditionWithFilter(): void
    {
        $data = [
            ['id' => 5, 'name' => 5],
            ['id' => '9', 'name' => 'world'],
            ['id' => 'haha', 'name' => '0'],
        ];

        $result = InCondition::handle($data, 'id', static fn ($v): int => (int) $v);
        $json = <<<'eot'
            [
                5,
                9,
                0
            ]
            eot;

        self::assertSame(
            $json,
            $this->varJson($result)
        );

        $result = InCondition::handle($data, 'name', static fn ($v): string => (string) $v);
        $json = <<<'eot'
            [
                "5",
                "world",
                "0"
            ]
            eot;

        self::assertSame(
            $json,
            $this->varJson($result)
        );
    }

    public function testInConditionArrayIsInvalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Data item must be array.'
        );

        InCondition::handle([1, 2], 0);
    }
}

class ArrMyArray implements IArray
{
    public function toArray(): array
    {
        return ['hello' => 'IArray'];
    }
}

class ArrMyJson implements IJson
{
    public function toJson(?int $config = null): string
    {
        if (null === $config) {
            $config = JSON_UNESCAPED_UNICODE;
        }

        return json_encode(['hello' => 'IJson'], $config);
    }
}

class ArrMyJsonSerializable implements \JsonSerializable
{
    public function jsonSerialize(): mixed
    {
        return ['hello' => 'JsonSerializable'];
    }
}

class ArrMyException extends \Exception {}

class ArrMyJsonSerializableWithException implements \JsonSerializable
{
    public function jsonSerialize(): mixed
    {
        throw new ArrMyException('json exception');
    }
}
