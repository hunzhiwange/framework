<?php

declare(strict_types=1);

namespace Tests\Validate\Validator;

use Leevel\Validate\Validator;
use Tests\TestCase;

#[Api([
    'zh-CN:title' => '验证器.两个字段是否不同',
    'path' => 'validate/validator/different',
])]
final class DifferentTest extends TestCase
{
    /**
     * @dataProvider baseUseProvider
     *
     * @param mixed $value
     * @param mixed $valueCompare
     */
    #[Api([
        'zh-CN:title' => '验证通过的数据',
        'zh-CN:description' => <<<'EOT'
以下是通过的校验数据示例。

``` php
{[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Validate\Validator\DifferentTest::class, 'baseUseProvider')]}
```

上面的数据是测试的数据提供者。
EOT,
    ])]
    public function testBaseUse($value, $valueCompare, string $param): void
    {
        $validate = new Validator(
            [
                'name' => $value,
                'name2' => $valueCompare,
                'name3' => new \stdClass(),
            ],
            [
                'name' => 'different:'.$param,
            ]
        );

        static::assertTrue($validate->success());
    }

    public static function baseUseProvider(): array
    {
        return [
            ['foo', 'foo2', 'name2'],
            ['bar', 'bar2', 'name2'],
            [new \stdClass(), new \stdClass(), 'name2'], // 非全等
            [new \stdClass(), '', 'name3'], // 非全等
            [['foo', 'bar'], '', 'name3'],
        ];
    }

    /**
     * @dataProvider badProvider
     *
     * @param mixed $value
     * @param mixed $valueCompare
     */
    #[Api([
        'zh-CN:title' => '未验证通过的数据',
        'zh-CN:description' => <<<'EOT'
以下是未通过的校验数据示例。

``` php
{[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Validate\Validator\DifferentTest::class, 'badProvider')]}
```

上面的数据是测试的数据提供者。
EOT,
    ])]
    public function testBad($value, $valueCompare, string $param): void
    {
        $validate = new Validator(
            [
                'name' => $value,
                'name2' => $valueCompare,
                'name3' => 'test',
            ],
            [
                'name' => 'different:'.$param,
            ]
        );

        static::assertFalse($validate->success());
    }

    public static function badProvider(): array
    {
        return [
            ['foo', 'foo', 'name2'],
            ['bar', 'bar', 'name2'],
            ['test', '', 'name3'],
        ];
    }

    #[Api([
        'zh-CN:title' => 'different 参数缺失',
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
                'name' => 'different',
            ]
        );

        $validate->success();
    }
}
