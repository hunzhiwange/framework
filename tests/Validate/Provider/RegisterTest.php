<?php

declare(strict_types=1);

namespace Tests\Validate\Provider;

use Leevel\Di\Container;
use Leevel\Validate\IValidate;
use Leevel\Validate\Provider\Register;
use Leevel\Validate\Validate;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class RegisterTest extends TestCase
{
    protected function setUp(): void
    {
        $container = Container::singletons();
        $container->clear();

        $container->singleton('i18n', function (): \I18nMock {
            return new \I18nMock();
        });
    }

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
                            10
                        ]
                    ]
                ]
            }
            eot;

        static::assertTrue($validator->success());
        static::assertFalse($validator->fail());
        static::assertSame([], $validator->error());
        static::assertSame([], $validator->getMessage());
        static::assertSame(['name' => '小牛哥'], $validator->getData());

        static::assertSame(
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
