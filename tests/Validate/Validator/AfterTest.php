<?php

declare(strict_types=1);

namespace Tests\Validate\Validator;

use Leevel\Validate\Validator;
use Tests\TestCase;

#[Api([
    'zh-CN:title' => '验证器.验证在给定日期之后',
    'path' => 'validate/validator/after',
])]
final class AfterTest extends TestCase
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
{[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Validate\Validator\AfterTest::class, 'baseUseProvider')]}
```

上面的数据是测试的数据提供者。
EOT,
    ])]
    public function testBaseUse($value, string $param): void
    {
        $validate = new Validator(
            [
                'name' => $value,
                'name2' => '2018-08-10',
            ],
            [
                'name' => 'after:'.$param,
            ]
        );

        static::assertTrue($validate->success());
    }

    public static function baseUseProvider(): array
    {
        return [
            ['2018-08-15', '2018-08-14'],
            ['2018-08-15', 'name2'],
            ['2018-08-15', '2018-08-14|date_format:Y-m-d'],
        ];
    }

    /**
     * @dataProvider badProvider
     *
     * @param mixed $value
     */
    #[Api([
        'zh-CN:title' => '未验证通过的数据',
        'zh-CN:description' => <<<'EOT'
以下是未通过的校验数据示例。

``` php
{[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Validate\Validator\AfterTest::class, 'badProvider')]}
```

上面的数据是测试的数据提供者。
EOT,
    ])]
    public function testBad($value, string $param): void
    {
        $validate = new Validator(
            [
                'name' => $value,
            ],
            [
                'name' => 'after:'.$param,
            ]
        );

        static::assertFalse($validate->success());
    }

    public static function badProvider(): array
    {
        return [
            ['2018-08-14', '2018-08-14'],
            ['2018-08-14', '2018-08-15'],
            ['2018-08-15', 'name2'],
            ['2018-08-15', '2018-08-14|date_format:Y-m'],
            [new \stdClass(), '1.1'],
            [[], '2018-08-15'],
            [true, '2018-08-15'],
            [false, '2018-08-15'],
        ];
    }

    #[Api([
        'zh-CN:title' => '日期格式化不一致无法通过验证',
    ])]
    public function testMakeDateTimeFormatWithNewDateTimeExceptionError(): void
    {
        $validate = new Validator(
            [
                'name' => '2018-08-10',
            ],
            [
                'name' => 'after:foobar|date_format:y',
            ]
        );

        static::assertFalse($validate->success());
    }

    #[Api([
        'zh-CN:title' => 'after 参数缺失',
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
                'name' => 'after',
            ]
        );

        $validate->success();
    }
}
