<?php

declare(strict_types=1);

namespace Tests\Validate\Validator;

use Leevel\Validate\Validator;
use Tests\TestCase;

#[Api([
    'zh-CN:title' => '验证器.验证值下限',
    'path' => 'validate/validator/min',
])]
final class MinTest extends TestCase
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
{[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Validate\Validator\MinTest::class, 'baseUseProvider')]}
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
                'name' => 'min:'.$param,
            ]
        );

        static::assertTrue($validate->success());
    }

    public static function baseUseProvider(): array
    {
        return [
            [3, 2],
            [1.5, '1.1'],
            [2, '1.5'],
            [3, '1.5'],
            [4, '1.5'],
            [4.5, '1.5'],
            ['b', 'a'],
            ['c', 'a'],
            ['foo', 'bar'],
            [1.1, '1.1'],
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
{[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Validate\Validator\MinTest::class, 'badProvider')]}
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
                'name' => 'min:'.$param,
            ]
        );

        static::assertFalse($validate->success());
    }

    public static function badProvider(): array
    {
        return [
            [2, 3],
            ['1.5', '2'],
            ['1.5', '3'],
            ['1.5', '4'],
            ['1.5', '4.5'],
            ['a', 'b'],
            ['a', 'c'],
            ['bar', 'foo'],
        ];
    }

    #[Api([
        'zh-CN:title' => 'min 参数缺失',
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
                'name' => 'min',
            ]
        );

        $validate->success();
    }
}
