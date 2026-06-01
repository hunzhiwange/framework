<?php

declare(strict_types=1);

namespace Tests\Validate\Validator;

use Leevel\Kernel\Utils\Api;
use Leevel\Validate\Validator;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

#[Api([
    'zh-CN:title' => '验证器.验证数据最小长度',
    'path' => 'validate/validator/min-length',
])]
final class MinLengthTest extends TestCase
{
    #[DataProvider('provideBaseUseCases')]
    #[Api([
        'zh-CN:title' => '验证通过的数据',
        'zh-CN:description' => <<<'EOT'
以下是通过的校验数据示例。

``` php
{[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Validate\Validator\MinLengthTest::class, 'baseUseProvider')]}
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
                'name' => 'min_length:'.$param,
            ]
        );

        self::assertTrue($validate->success());
    }

    public static function provideBaseUseCases(): iterable
    {
        return [
            [2, 1],
            ['中国', 2],
            ['中国', 1],
            ['成都', 2],
            ['hello', 5],
            ['hello', 4],
            ['foo', 3],
            ['world', 5],
            ['中国no1', 5],
            [true, 1],
            [false, 0],
            [null, 0],
        ];
    }

    #[DataProvider('provideBadCases')]
    #[Api([
        'zh-CN:title' => '未验证通过的数据',
        'zh-CN:description' => <<<'EOT'
以下是未通过的校验数据示例。

``` php
{[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Validate\Validator\MinLengthTest::class, 'badProvider')]}
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
                'name' => 'min_length:'.$param,
            ]
        );

        self::assertFalse($validate->success());
    }

    public static function provideBadCases(): iterable
    {
        return [
            [2, 2],
            ['中国', 3],
            ['成都', 3],
            ['hello', 6],
            ['foo', 4],
            ['world', 6],
            ['中国no1', 6],
            [new \stdClass(), 0],
            [['foo', 'bar'], 0],
            [[1, 2], 0],
            [1, 2],
            [[[], []], 0],
            [true, 2],
            [false, 1],
        ];
    }
}
