<?php

declare(strict_types=1);

namespace Tests\Validate\Provider;

use Leevel\Di\Container;
use Leevel\Validate\IValidate;
use Leevel\Validate\Provider\Register;
use Leevel\Validate\Validate;
use Tests\TestCase;

final class RegisterTest extends TestCase
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

    public function testBaseUse(): void
    {
        $test = new Register($container = $this->createContainer());
        $test->register();
        $container->alias($test->providers());

        // validate
        $validate = $container->make('validate');
        $this->assertInstanceof(IValidate::class, $validate);
        $validator = $validate->make(
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

        self::assertTrue($validator->success());
        self::assertFalse($validator->fail());
        self::assertSame([], $validator->error());
        self::assertSame([], $validator->getMessage());
        self::assertSame(['name' => '小牛哥'], $validator->getData());

        self::assertSame(
            $rule,
            $this->varJson(
                $validator->getRule()
            )
        );

        // alias
        $validate = $container->make(Validate::class);
        $this->assertInstanceof(IValidate::class, $validate);
    }

    protected function createContainer(): Container
    {
        return new Container();
    }
}
