<?php

declare(strict_types=1);

namespace Tests\Validate\Validator;

use Leevel\Kernel\Utils\Api;
use Leevel\Validate\Validator;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

#[Api([
    'zh-CN:title' => '验证器.是否处于某个范围',
    'path' => 'validate/validator/in',
])]
final class InTest extends TestCase
{
    #[DataProvider('provideBaseUseCases')]
    #[Api([
        'zh-CN:title' => '验证通过的数据',
        'zh-CN:description' => <<<'EOT'
以下是通过的校验数据示例。

``` php
{[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Validate\Validator\InTest::class, 'baseUseProvider')]}
```

上面的数据是测试的数据提供者。
EOT,
    ])]
    public function testBaseUse($value, string $param): void
    {
        $validate = new Validator(
            [
                'name' => $value,
            ],
            [
                'name' => 'in:'.$param,
            ]
        );

        self::assertTrue($validate->success());
    }

    public static function provideBaseUseCases(): iterable
    {
        return [
            [1, '1,5'],
            [2, '1,2,5'],
            [3, '1,3,5'],
            [4, '1,4,5'],
            [4.5, '1,4.5,5'],
            ['b', 'a,b,z'],
            ['c', 'a,c,z'],
            [1, '1,5'],
            [5, '1,5'],
            ['a', 'a,z'],
            ['z', 'a,z'],
        ];
    }

    #[DataProvider('provideBadCases')]
    #[Api([
        'zh-CN:title' => '未验证通过的数据',
        'zh-CN:description' => <<<'EOT'
以下是未通过的校验数据示例。

``` php
{[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Validate\Validator\InTest::class, 'badProvider')]}
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
                'name' => 'in:'.$param,
            ]
        );

        self::assertFalse($validate->success());
    }

    public static function provideBadCases(): iterable
    {
        return [
            ['0.1', '1,5'],
            ['5.5', '1,5'],
            ['8', '1,5'],
            ['c', 'h,t'],
            ['w', 'h,t'],
            ['c', 'foo,bar'],
            ['w', 'h,hello,world,t'],
        ];
    }

    #[Api([
        'zh-CN:title' => 'in 参数缺失',
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
                'name' => 'in',
            ]
        );

        $validate->success();
    }
}
