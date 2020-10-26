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

namespace Tests\Validate\Proxy;

use I18nMock;
use Leevel\Di\Container;
use Leevel\Validate\Proxy\Validate as ProxyValidate;
use Leevel\Validate\Validate;
use Tests\TestCase;

class ValidateTest extends TestCase
{
    protected function setUp(): void
    {
        $this->tearDown();
    }

    protected function tearDown(): void
    {
        $container = Container::singletons();
        $container->clear();
        $container->singleton('i18n', function (): I18nMock {
            return new I18nMock();
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

        $this->assertTrue($validate->success());
        $this->assertFalse($validate->fail());
        $this->assertSame([], $validate->error());
        $this->assertSame([], $validate->getMessage());
        $this->assertSame(['name' => '小牛哥'], $validate->getData());

        $this->assertSame(
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

        $this->assertTrue($validate->success());
        $this->assertFalse($validate->fail());
        $this->assertSame([], $validate->error());
        $this->assertSame([], $validate->getMessage());
        $this->assertSame(['name' => '小牛哥'], $validate->getData());

        $this->assertSame(
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
