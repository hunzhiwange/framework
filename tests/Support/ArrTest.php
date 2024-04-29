<?php

declare(strict_types=1);

namespace Tests\Support;

use Leevel\Kernel\Utils\Api;
use Leevel\Support\Arr;
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
        self::assertTrue(Arr::normalize(true));
        self::assertSame(['a', 'b'], Arr::normalize('a,b'));
        self::assertSame(['a', 'b'], Arr::normalize(['a', 'b']));
        self::assertSame(['a'], Arr::normalize(['a', '']));
        self::assertSame(['a'], Arr::normalize(['a', ''], ',', true));
        self::assertSame(['a', ' 0 '], Arr::normalize(['a', ' 0 '], ',', true));
        self::assertSame(['a', '0'], Arr::normalize(['a', ' 0 '], ','));
    }

    #[Api([
        'zh-CN:title' => 'normalize 格式化字符串',
    ])]
    public function testNormalize(): void
    {
        $result = Arr::normalize('hello');

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
        $result = Arr::normalize('hello,world');

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
        $result = Arr::normalize(['hello', 'world']);

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
        $result = Arr::normalize(['hello', 'world', ' ', '0']);

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
        'zh-CN:title' => 'normalize 格式化数组不过滤空格',
    ])]
    public function testNormalizeArrNotFilterEmpty(): void
    {
        $result = Arr::normalize(['hello', 'world', ' ', '0'], ',', true);

        $json = <<<'eot'
            [
                "hello",
                "world",
                " "
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
        $result = Arr::normalize(false);

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
        $result = Arr::only(['input' => 'test', 'foo' => 'bar', 'hello' => 'world'], ['input', 'hello', 'notfound']);

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
        $result = Arr::except(['input' => 'test', 'foo' => 'bar', 'hello' => 'world'], ['input', 'hello', 'notfound']);

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

        $result = Arr::filter($sourceData, $rule);

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

        $result = Arr::filter($sourceData, $rule);

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

        Arr::filter($sourceData, $rule);
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

        Arr::filter($sourceData, $rule);
    }

    #[Api([
        'zh-CN:title' => 'filter 数据过滤默认不处理 NULL 值',
    ])]
    public function testFilterWithoutMust(): void
    {
        $sourceData = ['foo' => null];
        $rule = ['foo' => ['intval']];

        $result = Arr::filter($sourceData, $rule);

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

        $result = Arr::filter($sourceData, $rule);

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
        self::assertTrue(Arr::shouldJson(['foo' => 'bar']));
        self::assertTrue(Arr::shouldJson(new ArrMyArray()));
        self::assertTrue(Arr::shouldJson(new ArrMyJson()));
        self::assertTrue(Arr::shouldJson(new ArrMyJsonSerializable()));
    }

    #[Api([
        'zh-CN:title' => 'convertJson 转换 JSON 数据',
    ])]
    public function testConvertJson(): void
    {
        self::assertSame('{"foo":"bar"}', Arr::convertJson(['foo' => 'bar']));
        self::assertSame('{"foo":"bar"}', Arr::convertJson(['foo' => 'bar'], JSON_THROW_ON_ERROR));
        self::assertSame('{"hello":"IArray"}', Arr::convertJson(new ArrMyArray()));
        self::assertSame('{"hello":"IJson"}', Arr::convertJson(new ArrMyJson()));
        self::assertSame('{"hello":"JsonSerializable"}', Arr::convertJson(new ArrMyJsonSerializable()));
        self::assertSame('{"成":"都"}', Arr::convertJson(['成' => '都']));
        self::assertSame('{"\u6210":"\u90fd"}', Arr::convertJson(['成' => '都'], 0));
    }

    public function testConvertJsonWithInvalidUtf8Characters(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Malformed UTF-8 characters, possibly incorrectly encoded'
        );

        Arr::convertJson("\xB1\x31");
    }

    public function testConvertJsonWithInvalidUtf8CharactersAndThrowJsonException(): void
    {
        $this->expectException(\JsonException::class);
        $this->expectExceptionMessage(
            'Malformed UTF-8 characters, possibly incorrectly encoded'
        );

        Arr::convertJson("\xB1\x31", JSON_THROW_ON_ERROR);
    }

    public function testConvertJsonJsonSerializeThrowException(): void
    {
        $this->expectException(ArrMyException::class);
        $this->expectExceptionMessage(
            'json exception'
        );

        Arr::convertJson(new ArrMyJsonSerializableWithException(), JSON_THROW_ON_ERROR);
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

        $result = Arr::inCondition($data, 'id');
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

        $result = Arr::inCondition($data, 'name');
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

        $result = Arr::inCondition($dataDemo2, 0);
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

        $result = Arr::inCondition($data, 'id');
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

        $result = Arr::inCondition($data, 'name');
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

        $result = Arr::inCondition($data, 'id');
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

        $result = Arr::inCondition($data, 'name');
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

        $result = Arr::inCondition($data, 'id', static fn ($v): int => (int) $v);
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

        $result = Arr::inCondition($data, 'name', static fn ($v): string => (string) $v);
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

        Arr::inCondition([1, 2], 0);
    }

    public function testArrNotFound(): void
    {
        $this->expectException(\Error::class);
        $this->expectExceptionMessage('Class "Leevel\\Support\\Arr\NotFound" not found');

        self::assertTrue(Arr::notFound());
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
