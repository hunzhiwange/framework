<?php

declare(strict_types=1);

namespace Tests\Validate\Proxy;

use Leevel\Di\Container;
use Leevel\Validate\Proxy\Validate as ProxyValidate;
use Leevel\Validate\Validate;
use Tests\TestCase;

final class ValidateTest extends TestCase
{
    #[\Override]
    protected function setUp(): void
    {
        $this->tearDown();
    }

    #[\Override]
    protected function tearDown(): void
    {
        $container = Container::singletons();
        $container->clear();
        $container->singleton('i18n', static function (): \I18nMock {
            return new \I18nMock();
        });

        Validate::initMessages();
    }

    public function testBaseUse(): void
    {
        $container = $this->createContainer();
        $validateFactory = $this->createValidate($container);
        $container->singleton('validate', static function () use ($validateFactory): Validate {
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

        self::assertTrue($validate->success());
        self::assertFalse($validate->fail());
        self::assertSame([], $validate->error());
        self::assertSame([], $validate->getMessage());
        self::assertSame(['name' => '小牛哥'], $validate->getData());

        self::assertSame(
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
        $container->singleton('validate', static function () use ($validateFactory): Validate {
            return $validateFactory;
        });

        $validate = ProxyValidate::proxy()->make(
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

        self::assertTrue($validate->success());
        self::assertFalse($validate->fail());
        self::assertSame([], $validate->error());
        self::assertSame([], $validate->getMessage());
        self::assertSame(['name' => '小牛哥'], $validate->getData());

        self::assertSame(
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
