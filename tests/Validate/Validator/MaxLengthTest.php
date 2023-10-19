<?php

declare(strict_types=1);

namespace Tests\Validate\Validator;

use Leevel\Validate\Validator;
use Tests\TestCase;

#[Api([
    'zh-CN:title' => '验证器.验证数据最大长度',
    'path' => 'validate/validator/maxlength',
])]
final class MaxLengthTest extends TestCase
{
    /**
     * @dataProvider baseUseProvider
     *
     * @param mixed $value
     * @param mixed $param
     */
    #[Api([
        'zh-CN:title' => '验证通过的数据',
        'zh-CN:description' => <<<'EOT'
以下是通过的校验数据示例。

``` php
{[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Validate\Validator\MaxLengthTest::class, 'baseUseProvider')]}
```

上面的数据是测试的数据提供者。
EOT,
    ])]
    public function testBaseUse($value, $param): void
    {
        $validate = new Validator(
            [
                'name' => $value,
            ],
            [
                'name' => 'max_length:'.$param,
            ]
        );

        static::assertTrue($validate->success());
    }

    public static function baseUseProvider(): array
    {
        return [
            [2, 1],
            ['中国', 2],
            ['中国', 3],
            ['成都', 2],
            ['hello', 5],
            ['hello', 6],
            ['foo', 3],
            ['world', 5],
            ['中国no1', 5],
            [true, 1],
            [false, 0],
            [null, 0],
        ];
    }

    /**
     * @dataProvider badProvider
     *
     * @param mixed $value
     * @param mixed $param
     */
    #[Api([
        'zh-CN:title' => '未验证通过的数据',
        'zh-CN:description' => <<<'EOT'
以下是未通过的校验数据示例。

``` php
{[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Validate\Validator\MaxLengthTest::class, 'badProvider')]}
```

上面的数据是测试的数据提供者。
EOT,
    ])]
    public function testBad($value, $param): void
    {
        $validate = new Validator(
            [
                'name' => $value,
            ],
            [
                'name' => 'max_length:'.$param,
            ]
        );

        static::assertFalse($validate->success());
    }

    public static function badProvider(): array
    {
        return [
            [2, 0],
            ['中国', 1],
            ['成都', 1],
            ['hello', 4],
            ['foo', 2],
            ['world', 4],
            ['中国no1', 4],
            [new \stdClass(), 0],
            [['foo', 'bar'], 0],
            [[1, 2], 0],
            [1, 0],
            [[[], []], 0],
            [true, 0],
        ];
    }

    #[Api([
        'zh-CN:title' => 'max_length 参数缺失',
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
                'name' => 'max_length',
            ]
        );

        $validate->success();
    }
}
