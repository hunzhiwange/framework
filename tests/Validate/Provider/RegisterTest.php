<?php

declare(strict_types=1);

/*
 * This file is part of the ************************ package.
 * _____________                           _______________
 *  ______/     \__  _____  ____  ______  / /_  _________
 *   ____/ __   / / / / _ \/ __`\/ / __ \/ __ \/ __ \___
 *    __/ / /  / /_/ /  __/ /  \  / /_/ / / / / /_/ /__
 *      \_\ \_/\____/\___/_/   / / .___/_/ /_/ .___/
 *         \_\                /_/_/         /_/
 *
 * The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
 * (c) 2010-2020 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Validate\Provider;

use I18nMock;
use Leevel\Di\Container;
use Leevel\Validate\IValidate;
use Leevel\Validate\Provider\Register;
use Leevel\Validate\Validate;
use Tests\TestCase;

/**
 * register test.
 */
class RegisterTest extends TestCase
{
    protected function setUp(): void
    {
        $container = Container::singletons();
        $container->clear();

        $container->singleton('i18n', function (): I18nMock {
            return new I18nMock();
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
                'name'     => 'required|max_length:10',
            ],
            [
                'name'     => '用户名',
            ]
        );

        $rule = <<<'eot'
            {
                "name": [
                    "required",
                    "max_length:10"
                ]
            }
            eot;

        $this->assertTrue($validator->success());
        $this->assertFalse($validator->fail());
        $this->assertSame([], $validator->error());
        $this->assertSame([], $validator->getMessage());
        $this->assertSame(['name' => '小牛哥'], $validator->getData());

        $this->assertSame(
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
