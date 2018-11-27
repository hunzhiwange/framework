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
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Validate\Provider;

use Leevel\Di\Container;
use Leevel\Validate\IValidate;
use Leevel\Validate\Provider\Register;
use Tests\TestCase;

/**
 * register test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.08.26
 *
 * @version 1.0
 */
class RegisterTest extends TestCase
{
    public function testBaseUse()
    {
        $test = new Register($container = $this->createContainer());

        $test->register();

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
    }

    protected function createContainer(): Container
    {
        return new Container();
    }
}
