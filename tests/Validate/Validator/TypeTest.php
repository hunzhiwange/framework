<?php

declare(strict_types=1);

namespace Tests\Validate\Validator;

use Leevel\Kernel\Utils\Api;
use Leevel\Validate\Helper\Type;
use Leevel\Validate\Validator;
use Tests\TestCase;

#[Api([
    'zh-CN:title' => '验证器.数据类型验证',
    'path' => 'validate/validator/type',
    'zh-CN:description' => <<<'EOT'
数据类型验证底层核心为函数 `Leevel\Support\Type\Type`，相对于 PHP 提供的 `gettype` 更加强大。
EOT,
])]
final class TypeTest extends TestCase
{
    /**
     * @dataProvider baseUseProvider
     *
     * @param mixed $value
     */
    #[Api([
        'zh-CN:title' => '验证通过的数据',
        'zh-CN:description' => <<<'EOT'
以下是通过的校验数据示例。

``` php
{[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Validate\Validator\TypeTest::class, 'baseUseProvider')]}
```

上面的数据是测试的数据提供者。
EOT,
    ])]
    public function testBaseUse($value, string $type): void
    {
        $validate = new Validator(
            [
                'name' => $value,
            ],
            [
                'name' => 'type:'.$type,
            ]
        );

        static::assertTrue($validate->success());
    }

    public static function baseUseProvider(): array
    {
        $testFile = __DIR__.'/../assert/test.txt';
        $resource = fopen($testFile, 'r');

        // 主要为 is_xxx 系列
        // https://www.php.net/manual/zh/function.is-array.php
        return [
            [true, 'bool'],
            [true, 'bool'],
            [1.5, 'double'],
            [6.00, 'double'],
            ['中国', 'string'],
            ['成都no1', 'string'],
            [['foo', 'bar'], 'array'],
            [['hello', 'world'], 'array'],
            [['hello', 'world'], 'array:string'],
            [['hello', 'world'], 'array:int:string'],
            [['hello' => 'world', 'world' => 'world'], 'array:string:string'],
            [new \stdClass(), 'object'],
            [new Type1(), 'object'],
            [$resource, 'resource'],
            [null, 'NULL'],
        ];
    }

    /**
     * @dataProvider badProvider
     *
     * @param mixed $value
     * @param mixed $type
     */
    #[Api([
        'zh-CN:title' => '未验证通过的数据',
        'zh-CN:description' => <<<'EOT'
以下是未通过的校验数据示例。

``` php
{[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Validate\Validator\TypeTest::class, 'badProvider')]}
```

上面的数据是测试的数据提供者。
EOT,
    ])]
    public function testBad($value, $type): void
    {
        $validate = new Validator(
            [
                'name' => $value,
            ],
            [
                'name' => 'type:'.$type,
            ]
        );

        static::assertFalse($validate->success());
    }

    public static function badProvider(): array
    {
        return [
            ['not numeric', 'errorType'],
            [[], 'errorType'],
            [new \stdClass(), 'errorType'],
            [['foo', 'bar'], 'errorType'],
            [[1, 2], 'errorType'],
            ['tel:+1-816-555-1212', 'errorType'],
            ['foo', 'errorType'],
            ['bar', 'errorType'],
            ['urn:oasis:names:specification:docbook:dtd:xml:4.1.2', 'errorType'],
            ['world', 'errorType'],
            [null, 'errorType'],
            ['errorType', 1],
        ];
    }

    #[Api([
        'zh-CN:title' => 'type 参数缺失',
    ])]
    public function testMissParam(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Missing the first element of param.'
        );

        $validate = new Validator(
            [
                'name' => '',
            ],
            [
                'name' => 'type',
            ]
        );

        $validate->success();
    }

    public function test1(): void
    {
        $result = Type::handle('foo', [1]);
        static::assertFalse($result);
    }
}

class Type1
{
}
