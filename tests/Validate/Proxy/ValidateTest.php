<?php

declare(strict_types=1);

namespace Tests\Validate\Proxy;

use Leevel\Di\Container;
use Leevel\Validate\Proxy\Validate as ProxyValidate;
use Leevel\Validate\Validate;
use Tests\TestCase;

final class ValidateTest extends TestCase
{
    protected function setUp(): void
    {
        $this->tearDown();
    }

    protected function tearDown(): void
    {
        $container = Container::singletons();
        $container->clear();
        $container->singleton('i18n', function (): \I18nMock {
            return new \I18nMock();
        });

        Validate::initMessages();
    }

    public function testBaseUse(): void
    {
        $container = $this->createContainer();
        $validateFactory = $this->createValidate($container);
        $container->singleton('validate', function () use ($validateFactory): Validate {
            return $validateFactory;
        });

        $validate = $validateFactory->make(
            [
                'name' => '小牛哥',
            ],
            [
                'name' => 'required|max_length:10',
            ],
            [
                'name' => '用户名',
            ]
        );

        $rule = <<<'eot'
            {
                "name": [
                    [
                        "required",
                        []
                    ],
                    [
                        "max_length",
                        [
                            "10"
                        ]
                    ]
                ]
            }
            eot;

        static::assertTrue($validate->success());
        static::assertFalse($validate->fail());
        static::assertSame([], $validate->error());
        static::assertSame([], $validate->getMessage());
        static::assertSame(['name' => '小牛哥'], $validate->getData());

        static::assertSame(
            $rule,
            $this->varJson(
                $validate->getRule()
            )
        );
    }

    public function testProxy(): void
    {
        $container = $this->createContainer();
        $validateFactory = $this->createValidate($container);
        $container->singleton('validate', function () use ($validateFactory): Validate {
            return $validateFactory;
        });

        $validate = ProxyValidate::make(
            [
                'name' => '小牛哥',
            ],
            [
                'name' => 'required|max_length:10',
            ],
            [
                'name' => '用户名',
            ]
        );

        $rule = <<<'eot'
            {
                "name": [
                    [
                        "required",
                        []
                    ],
                    [
                        "max_length",
                        [
                            "10"
                        ]
                    ]
                ]
            }
            eot;

        static::assertTrue($validate->success());
        static::assertFalse($validate->fail());
        static::assertSame([], $validate->error());
        static::assertSame([], $validate->getMessage());
        static::assertSame(['name' => '小牛哥'], $validate->getData());

        static::assertSame(
            $rule,
            $this->varJson(
                $validate->getRule()
            )
        );
    }

    protected function createValidate(Container $container): Validate
    {
        return new Validate($container);
    }

    protected function createContainer(): Container
    {
        return Container::singletons();
    }
}
