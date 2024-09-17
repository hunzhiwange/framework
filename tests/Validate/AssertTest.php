<?php

declare(strict_types=1);

namespace Tests\Validate;

use Leevel\Di\Container;
use Leevel\Kernel\Utils\Api;
use Leevel\Validate\Assert;
use Leevel\Validate\AssertException;
use Tests\TestCase;

#[Api([
    'zh-CN:title' => '断言',
    'path' => 'validate/assert',
    'zh-CN:description' => <<<'EOT'
这里为系统提供的基础的断言功能，断言的规则与验证器共享校验规则。
EOT,
])]
final class AssertTest extends TestCase
{
    #[\Override]
    protected function setUp(): void
    {
        $container = Container::singletons();
        $container->clear();

        $container->singleton('i18n', static function (): \I18nMock {
            return new \I18nMock();
        });
    }

    #[\Override]
    protected function tearDown(): void
    {
        Container::singletons()->clear();
    }

    #[Api([
        'zh-CN:title' => '基本断言测试',
        'zh-CN:description' => <<<'EOT'
断言和验证器共享规则，所以可以直接参考验证器有哪些规则。
EOT,
    ])]
    public function testBaseUse(): void
    {
        Assert::make(1)
            ->notEmpty()
        ;
        Assert::make(55)
            ->notEmpty()
        ;
        Assert::make(66)
            ->notEmpty()
        ;
        Assert::make(4)
            ->lessThan([5])
        ;
        self::assertSame(1, 1);
    }

    #[Api([
        'zh-CN:title' => '断言失败默认错误消息',
    ])]
    public function testAssertFailedWithDefaultMessage(): void
    {
        $this->expectException(AssertException::class);
        $this->expectExceptionMessage(
            '不能为空'
        );

        Assert::make(0)
            ->notEmpty()
        ;
    }

    #[Api([
        'zh-CN:title' => '断言失败自定义消息',
    ])]
    public function testAssertFailedWithCustomMessage(): void
    {
        $this->expectException(AssertException::class);
        $this->expectExceptionMessage(
            '值不能为空'
        );

        Assert::make(0)
            ->notEmpty('值不能为空')
        ;
    }

    #[Api([
        'zh-CN:title' => '可选断言支持',
        'zh-CN:description' => <<<'EOT'
如果值为 `null` 直接返回正确结果。
EOT,
    ])]
    public function testAssertOptional(): void
    {
        Assert::make(null)
            ->optionalNotEmpty()
        ;

        self::assertSame(1, 1);
    }

    #[Api([
        'zh-CN:title' => '可选断言失败',
    ])]
    public function testAssertOptionalFailed(): void
    {
        $this->expectException(AssertException::class);
        $this->expectExceptionMessage(
            '不能为空'
        );

        Assert::make(0)
            ->optionalNotEmpty()
        ;
    }

    #[Api([
        'zh-CN:title' => '断言支持多个校验',
        'zh-CN:description' => <<<'EOT'
必须每一个都满足规则才算校验成功。
EOT,
    ])]
    public function testAssertMulti(): void
    {
        Assert::make([3, ['hello'], 'bar', 'yes'], true)
            ->multiNotEmpty()
        ;

        self::assertSame(1, 1);
    }

    #[Api([
        'zh-CN:title' => '断言支持多个校验',
        'zh-CN:description' => <<<'EOT'
必须每一个都满足规则才算校验成功。
EOT,
    ])]
    public function testAssertMultiFailed(): void
    {
        $this->expectException(AssertException::class);
        $this->expectExceptionMessage(
            '不能为空'
        );

        Assert::make([3, ['hello'], '', 'yes'], true)
            ->multiNotEmpty()
        ;
    }

    #[Api([
        'zh-CN:title' => '断言支持多个校验也支持可选',
        'zh-CN:description' => <<<'EOT'
必须每一个都满足规则才算校验成功, 可选会跳过验证。
EOT,
    ])]
    public function testAssertMultiWithOptional(): void
    {
        Assert::make([null, ['hello'], 'bar', 'yes', null], true)
            ->optionalMultiNotEmpty()
        ;

        self::assertSame(1, 1);
    }

    #[Api([
        'zh-CN:title' => '断言支持链式表达式',
        'zh-CN:description' => <<<'EOT'
我们可以使用链式表达式来校验规则。

**make 原型**

``` php
Assert::make(mixed $value, bool $multiValue = false, ?string $field = null, bool $all = true, bool $lazy = false): static
```
EOT,
    ])]
    public function testAssertChain(): void
    {
        Assert::make(5)
            ->notEmpty()
            ->lessThan([7])
        ;

        self::assertSame(1, 1);
    }

    #[Api([
        'zh-CN:title' => '断言链式表达式支持可选和多个校验',
        'zh-CN:description' => <<<'EOT'
链式表达式数据值只支持单个，但是可以调用多个校验方法，系统做了统一兼容。
一般来说多个校验这种用法在链式调用中没有必要，如果调用了也是没有什么问题。
EOT,
    ])]
    public function testAssertChainSupportOptionalMulti(): void
    {
        Assert::make(5)
            ->notEmpty()
            ->lessThan([7])
            ->multiNotEmpty()
            ->optionalNotEmpty()
            ->optionalMultiNotEmpty()
        ;

        self::assertSame(1, 1);
    }

    #[Api([
        'zh-CN:title' => '断言支持延迟释放',
        'zh-CN:description' => <<<'EOT'
可以将所有错误几种抛出。

**lazy 原型**

``` php
Assert::lazy(mixed $value, bool $multiValue = false, ?string $field = null, bool $all = true): static
```
EOT,
    ])]
    public function testAssertLazyChain(): void
    {
        $result = Assert::lazy(3)
            ->notEmpty()
            ->lessThan([7])
            ->lessThan([8])
            ->lessThan([9])
            ->flush()
        ;

        $this->assert($result);
    }

    public function testAssertLazyChain2(): void
    {
        $result = Assert::lazy([3, 2, 5], true)
            ->notEmpty()
            ->lessThan([7])
            ->lessThan([8])
            ->lessThan([9])
            ->flush()
        ;

        $this->assert($result);
    }

    public function testAssertLazyChain3(): void
    {
        $this->expectException(AssertException::class);
        $this->expectExceptionMessage(
            '["必须小于 7","必须小于 8","必须小于 9"]'
        );

        $result = Assert::lazy(11)
            ->notEmpty()
            ->lessThan([7])
            ->lessThan([8])
            ->lessThan([9])
            ->flush()
        ;

        $this->assert($result);
    }

    public function testAssertLazyChain4(): void
    {
        $this->expectException(AssertException::class);
        $this->expectExceptionMessage(
            '["必须小于 7","必须小于 8","必须小于 9"]'
        );

        $result = Assert::lazy([3, 22, 5], true)
            ->notEmpty()
            ->lessThan([7])
            ->lessThan([8])
            ->lessThan([9])
            ->flush()
        ;

        $this->assert($result);
    }

    public function testAssertLazyChain5(): void
    {
        $this->expectException(AssertException::class);
        $this->expectExceptionMessage(
            '["名字 必须小于 7","名字 必须小于 8","名字 必须小于 9"]'
        );

        $result = Assert::lazy([3, 22, 5], true, field: '名字')
            ->notEmpty()
            ->lessThan([7])
            ->lessThan([8])
            ->lessThan([9])
            ->flush()
        ;

        $this->assert($result);
    }

    public function testAssertLazyChainWithNotAll(): void
    {
        $this->expectException(AssertException::class);
        $this->expectExceptionMessage(
            '["5 not less than 3"]'
        );

        Assert::lazy(5, all: false)
            ->notEmpty()
            ->lessThan([2], '5 not less than 3')
            ->lessThan([8], '5 not less than 4')
            ->lessThan([9], '5 not less than 2')
            ->flush()
        ;
    }

    public function test1(): void
    {
        $this->expectException(AssertException::class);
        $this->expectExceptionMessage(
            '["必须小于 2"]'
        );

        Assert::lazy(5, all: false)
            ->notEmpty()
            ->lessThan([2])
            ->lessThan([8])
            ->lessThan([9])
            ->flush()
        ;
    }

    #[Api([
        'zh-CN:title' => '断言失败延迟释放',
    ])]
    public function testAssertLazyChainFailed(): void
    {
        $this->expectException(AssertException::class);
        $this->expectExceptionMessage(
            '["5 not less than 3","5 not less than 4","5 not less than 2"]'
        );

        Assert::lazy(5)
            ->notEmpty()
            ->lessThan([3], '5 not less than 3')
            ->lessThan([4], '5 not less than 4')
            ->lessThan([2], '5 not less than 2')
            ->flush()
        ;
    }

    #[Api([
        'zh-CN:title' => '断言失败延迟释放自定义格式化',
    ])]
    public function testAssertLazyChainFailedWithCustomFormat(): void
    {
        $this->expectException(AssertException::class);
        $this->expectExceptionMessage(
            '5 not less than 3'.PHP_EOL.
            '5 not less than 4'.PHP_EOL.
            '5 not less than 2'
        );

        Assert::lazy(5)
            ->notEmpty()
            ->lessThan([3], '5 not less than 3')
            ->lessThan([4], '5 not less than 4')
            ->lessThan([2], '5 not less than 2')
            ->flush(static function (array $error): string {
                return implode(PHP_EOL, $error);
            })
        ;
    }

    public function testAssertMultiWithInvalidFirstArgument(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Invalid first argument for multi assert.'
        );

        Assert::make('hello world', true)
            ->multiNotEmpty()
        ;
    }

    public function testAssertNotLazyChainWithNotAll(): void
    {
        $this->expectException(AssertException::class);
        $this->expectExceptionMessage(
            '5 not less than 3'
        );

        Assert::make(8, false, lazy: false, all: false)
            ->notEmpty()
            ->lessThan([3], '5 not less than 3')
            ->lessThan([4], '5 not less than 4')
            ->lessThan([2], '5 not less than 2')
            ->flush()
        ;
    }

    public function testAssertOptionalMultiAllWasNull(): void
    {
        Assert::make([null, null, null], true)
            ->optionalMultiNotEmpty()
        ;

        self::assertSame(1, 1);
    }

    public function testAssertExceptionReportable(): void
    {
        $e = new AssertException();
        self::assertFalse($e->reportable());
    }
}
