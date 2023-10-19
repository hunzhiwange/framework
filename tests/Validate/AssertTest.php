<?php

declare(strict_types=1);

namespace Tests\Validate;

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
    #[Api([
        'zh-CN:title' => '基本断言测试',
        'zh-CN:description' => <<<'EOT'
断言和验证器共享规则，所以可以直接参考验证器有哪些规则，排查掉依赖验证器自身的校验规则。

**支持格式**

``` php
Assert::foo($value, string $message);
Assert::foo($value, array $param, string $message);
```
EOT,
    ])]
    public function testBaseUse(): void
    {
        Assert::notEmpty(1);
        Assert::notEmpty(55);
        Assert::notEmpty(66);
        Assert::lessThan(4, [5]);
        static::assertSame(1, 1);
    }

    #[Api([
        'zh-CN:title' => '断言失败默认错误消息',
    ])]
    public function testAssertFailedWithDefaultMessage(): void
    {
        $this->expectException(\Leevel\Validate\AssertException::class);
        $this->expectExceptionMessage(
            'No exception messsage specified.'
        );

        Assert::notEmpty(0);
    }

    #[Api([
        'zh-CN:title' => '断言失败自定义消息',
    ])]
    public function testAssertFailedWithCustomMessage(): void
    {
        $this->expectException(\Leevel\Validate\AssertException::class);
        $this->expectExceptionMessage(
            'No exception messsage specified.'
        );

        Assert::notEmpty(0);
    }

    #[Api([
        'zh-CN:title' => '可选断言支持',
        'zh-CN:description' => <<<'EOT'
如果值为 `null` 直接返回正确结果。
EOT,
    ])]
    public function testAssertOptional(): void
    {
        Assert::optionalNotEmpty(null);

        static::assertSame(1, 1);
    }

    #[Api([
        'zh-CN:title' => '可选断言失败',
    ])]
    public function testAssertOptionalFailed(): void
    {
        $this->expectException(\Leevel\Validate\AssertException::class);
        $this->expectExceptionMessage(
            'No exception messsage specified.'
        );

        Assert::optionalNotEmpty(0);
    }

    #[Api([
        'zh-CN:title' => '断言支持多个校验',
        'zh-CN:description' => <<<'EOT'
必须每一个都满足规则才算校验成功。
EOT,
    ])]
    public function testAssertMulti(): void
    {
        Assert::multiNotEmpty([3, ['hello'], 'bar', 'yes']);

        static::assertSame(1, 1);
    }

    #[Api([
        'zh-CN:title' => '断言支持多个校验',
        'zh-CN:description' => <<<'EOT'
必须每一个都满足规则才算校验成功。
EOT,
    ])]
    public function testAssertMultiFailed(): void
    {
        $this->expectException(\Leevel\Validate\AssertException::class);
        $this->expectExceptionMessage(
            'No exception messsage specified.'
        );

        Assert::multiNotEmpty([3, ['hello'], '', 'yes']);
    }

    #[Api([
        'zh-CN:title' => '断言支持多个校验也支持可选',
        'zh-CN:description' => <<<'EOT'
必须每一个都满足规则才算校验成功, 可选会跳过验证，可选必须在最前面，即不支持 `multiOptional` 这种写法。
EOT,
    ])]
    public function testAssertMultiWithOptional(): void
    {
        Assert::optionalMultiNotEmpty([null, ['hello'], 'bar', 'yes', null]);

        static::assertSame(1, 1);
    }

    #[Api([
        'zh-CN:title' => '断言支持链式表达式',
        'zh-CN:description' => <<<'EOT'
我们可以使用链式表达式来校验规则。

**make 原型**

``` php
Assert::make($value, ?string $message)
```

第一个参数为待校验的值，第二个为默认校验失败消息，每一条验证规则也支持自己的失败消息。
EOT,
    ])]
    public function testAssertChain(): void
    {
        Assert::make(5, 'Assert success.')
            ->notEmpty()
            ->lessThan([7])
        ;

        static::assertSame(1, 1);
    }

    #[Api([
        'zh-CN:title' => '断言链式表达式支持可选和多个校验',
        'zh-CN:description' => <<<'EOT'
链式表达式数据值只支持单个，但是可以调用多个校验方法，系统做了统一兼容。一般来说多个校验这种用法在链式调用中没有必要，如果调用了也是没有什么问题。
EOT,
    ])]
    public function testAssertChainSupportOptionalMulti(): void
    {
        Assert::make(5, 'Assert success.')
            ->notEmpty()
            ->lessThan([7])
            ->multiNotEmpty()
            ->optionalNotEmpty()
            ->optionalMultiNotEmpty()
        ;

        static::assertSame(1, 1);
    }

    #[Api([
        'zh-CN:title' => '断言支持延迟释放',
        'zh-CN:description' => <<<'EOT'
可以将所有错误几种抛出。

**lazy 原型**

``` php
Assert::lazy($value, ?string $message, bool $all = true)
```

第一个参数为待校验的值，第二个为默认校验失败消息，第三个为是否全部验证，每一条验证规则也支持自己的失败消息。
EOT,
    ])]
    public function testAssertLazyChain(): void
    {
        $result = Assert::lazy(5, 'Assert success.')
            ->notEmpty()
            ->lessThan([7], '5 not less than 3')
            ->lessThan([8], '5 not less than 4')
            ->lessThan([9], '5 not less than 2')
            ->flush()
        ;

        $this->assert($result);
    }

    public function testAssertLazyChainWithNotAll(): void
    {
        $this->expectException(\Leevel\Validate\AssertException::class);
        $this->expectExceptionMessage(
            '["5 not less than 3"]'
        );

        Assert::lazy(5, 'Assert success.', false)
            ->notEmpty()
            ->lessThan([2], '5 not less than 3')
            ->lessThan([8], '5 not less than 4')
            ->lessThan([9], '5 not less than 2')
            ->flush()
        ;
    }

    #[Api([
        'zh-CN:title' => '断言失败延迟释放',
    ])]
    public function testAssertLazyChainFailed(): void
    {
        $this->expectException(\Leevel\Validate\AssertException::class);
        $this->expectExceptionMessage(
            '["5 not less than 3","5 not less than 4","5 not less than 2"]'
        );

        Assert::lazy(5, 'Assert success.')
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
        $this->expectException(\Leevel\Validate\AssertException::class);
        $this->expectExceptionMessage(
            '5 not less than 3'.PHP_EOL.
            '5 not less than 4'.PHP_EOL.
            '5 not less than 2'
        );

        Assert::lazy(5, 'Assert success.')
            ->notEmpty()
            ->lessThan([3], '5 not less than 3')
            ->lessThan([4], '5 not less than 4')
            ->lessThan([2], '5 not less than 2')
            ->flush(function (array $error): string {
                return implode(PHP_EOL, $error);
            })
        ;
    }

    public function testAssertMissingArgument(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Missing the first argument.'
        );

        Assert::notEmpty();
    }

    public function testAssertNotFoundRule(): void
    {
        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage(
            'Class `Leevel\\Validate\\Helper\\NotFound` is not exits.'
        );

        Assert::notFound(1);
    }

    public function testAssertMultiWithInvalidFirstArgument(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Invalid first argument for multi assert.'
        );

        Assert::multiNotEmpty('hello world');
    }

    public function testAssertNotLazyChainWithNotAll(): void
    {
        $this->expectException(\Leevel\Validate\AssertException::class);
        $this->expectExceptionMessage(
            '5 not less than 3'
        );

        Assert::make(8, null, false, false)
            ->notEmpty()
            ->lessThan([3], '5 not less than 3')
            ->lessThan([4], '5 not less than 4')
            ->lessThan([2], '5 not less than 2')
            ->flush()
        ;
    }

    public function testAssertOptionalMultiAllWasNull(): void
    {
        Assert::optionalMultiNotEmpty([null, null, null]);
        static::assertSame(1, 1);
    }

    public function testAssertOptionalMultiAllWasNullFailed(): void
    {
        $this->expectException(\Leevel\Validate\AssertException::class);
        $this->expectExceptionMessage(
            'No exception messsage specified.'
        );

        Assert::optionalMultiLessThan([null, 8, null], [5]);
    }

    public function testAssertExceptionReportable(): void
    {
        $e = new AssertException();
        static::assertFalse($e->reportable());
    }
}
