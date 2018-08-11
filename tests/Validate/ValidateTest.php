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

namespace Tests\Validate;

use Leevel\Validate\IValidate;
use Leevel\Validate\Validate;
use Tests\TestCase;

/**
 * validate test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.07.12
 *
 * @version 1.0
 */
class ValidateTest extends TestCase
{
    public function testBaseUse()
    {
        $validate = new Validate(
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

        $this->assertInstanceof(IValidate::class, $validate);

        $rule = <<<'eot'
array (
  'name' => 
  array (
    0 => 'required',
    1 => 'max_length:10',
  ),
)
eot;

        $this->assertTrue($validate->success());
        $this->assertFalse($validate->fail());
        $this->assertSame([], $validate->error());
        $this->assertSame([], $validate->getMessage());
        $this->assertSame(['name' => '小牛哥'], $validate->getData());

        $this->assertSame(
            $rule,
            $this->varExport(
                $validate->getRule()
            )
        );
    }

    public function testError()
    {
        $validate = new Validate(
            [
                'name' => '小牛哥',
            ],
            [
                'name'     => 'required|min_length:20',
            ],
            [
                'name'     => '用户名',
            ]
        );

        $error = <<<'eot'
array (
  'name' => 
  array (
    0 => '用户名 不满足最小长度 20',
  ),
)
eot;

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());

        $this->assertSame(
            $error,
            $this->varExport(
                $validate->error()
            )
        );
    }

    public function testData()
    {
        $validate = new Validate(
            [
                'name' => '中国',
            ],
            [
                'name'     => 'required|min_length:20',
            ],
            [
                'name'     => '用户名',
            ]
        );

        $error = <<<'eot'
array (
  'name' => 
  array (
    0 => '用户名 不满足最小长度 20',
  ),
)
eot;

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());

        $this->assertSame(
            $error,
            $this->varExport(
                $validate->error()
            )
        );

        $validate->data(['name' => '12345678901234567890']);

        $this->assertTrue($validate->success());
        $this->assertFalse($validate->fail());
    }

    public function testAddData()
    {
        $validate = new Validate(
            [
            ],
            [
                'name'     => 'required|min_length:20',
            ],
            [
                'name'     => '用户名',
            ]
        );

        $error = <<<'eot'
array (
  'name' => 
  array (
    0 => '用户名 不满足最小长度 20',
  ),
)
eot;

        $this->assertTrue($validate->success());
        $this->assertFalse($validate->fail());

        $validate->addData(['name' => '中国']);

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());

        $this->assertSame(
            $error,
            $this->varExport(
                $validate->error()
            )
        );
    }

    public function testRule()
    {
        $validate = new Validate(
            [
                'name' => '中国',
            ],
            [
            ],
            [
                'name'     => '用户名',
            ]
        );

        $error = <<<'eot'
array (
  'name' => 
  array (
    0 => '用户名 不满足最小长度 20',
  ),
)
eot;

        $this->assertTrue($validate->success());
        $this->assertFalse($validate->fail());

        $validate->rule(['name' => 'required|min_length:20']);

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());

        $this->assertSame(
            $error,
            $this->varExport(
                $validate->error()
            )
        );

        $validate->rule(['name' => 'required|max_length:20']);

        $this->assertTrue($validate->success());
        $this->assertFalse($validate->fail());
    }

    public function testRuleIf()
    {
        $validate = new Validate(
            [
                'name' => '中国',
            ],
            [
            ],
            [
                'name'     => '用户名',
            ]
        );

        $this->assertTrue($validate->success());
        $this->assertFalse($validate->fail());

        $validate->ruleIf(['name' => 'required|min_length:20'], function (array $data) {
            $this->assertSame(['name' => '中国'], $data);

            return false;
        });

        $rule = <<<'eot'
array (
)
eot;

        $this->assertSame(
            $rule,
            $this->varExport(
                $validate->getRule()
            )
        );

        $this->assertTrue($validate->success());
        $this->assertFalse($validate->fail());

        $validate->ruleIf(['name' => 'required|min_length:20'], function (array $data) {
            $this->assertSame(['name' => '中国'], $data);

            return true;
        });

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());

        $error = <<<'eot'
array (
  'name' => 
  array (
    0 => '用户名 不满足最小长度 20',
  ),
)
eot;

        $this->assertSame(
            $error,
            $this->varExport(
                $validate->error()
            )
        );
    }

    public function testAddRule()
    {
        $validate = new Validate(
            [
                'name' => '中国',
            ],
            [
            ],
            [
                'name'     => '用户名',
            ]
        );

        $this->assertTrue($validate->success());
        $this->assertFalse($validate->fail());

        $validate->addRule(['name' => 'required|min_length:20']);

        $rule = <<<'eot'
array (
  'name' => 
  array (
    0 => 'required',
    1 => 'min_length:20',
  ),
)
eot;

        $this->assertSame(
            $rule,
            $this->varExport(
                $validate->getRule()
            )
        );

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());

        $error = <<<'eot'
array (
  'name' => 
  array (
    0 => '用户名 不满足最小长度 20',
  ),
)
eot;

        $this->assertSame(
            $error,
            $this->varExport(
                $validate->error()
            )
        );
    }

    public function testAddRuleIf()
    {
        $validate = new Validate(
            [
                'name' => '中国',
            ],
            [
            ],
            [
                'name'     => '用户名',
            ]
        );

        $this->assertTrue($validate->success());
        $this->assertFalse($validate->fail());

        $validate->addRuleIf(['name' => 'required|min_length:20'], function (array $data) {
            $this->assertSame(['name' => '中国'], $data);

            return false;
        });

        $rule = <<<'eot'
array (
)
eot;

        $this->assertSame(
            $rule,
            $this->varExport(
                $validate->getRule()
            )
        );

        $this->assertTrue($validate->success());
        $this->assertFalse($validate->fail());

        $validate->addRuleIf(['name' => 'required|min_length:20'], function (array $data) {
            $this->assertSame(['name' => '中国'], $data);

            return true;
        });

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());

        $error = <<<'eot'
array (
  'name' => 
  array (
    0 => '用户名 不满足最小长度 20',
  ),
)
eot;

        $this->assertSame(
            $error,
            $this->varExport(
                $validate->error()
            )
        );
    }

    public function testMessage()
    {
        $validate = new Validate(
            [
                'name' => '中国',
            ],
            [
                'name'     => 'required|min_length:20',
            ],
            [
                'name'     => '用户名',
            ]
        );

        $error = <<<'eot'
array (
  'name' => 
  array (
    0 => '用户名 不满足最小长度 20',
  ),
)
eot;
        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());

        $this->assertSame(
            $error,
            $this->varExport(
                $validate->error()
            )
        );

        $validate->message(['min_length' => '{field} not min {rule}']);

        $error = <<<'eot'
array (
  'name' => 
  array (
    0 => '用户名 not min 20',
  ),
)
eot;
        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());

        $this->assertSame(
            $error,
            $this->varExport(
                $validate->error()
            )
        );

        $validate->addMessage(['min_length' => '{field} foo bar {rule}']);

        $error = <<<'eot'
array (
  'name' => 
  array (
    0 => '用户名 foo bar 20',
  ),
)
eot;
        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());

        $this->assertSame(
            $error,
            $this->varExport(
                $validate->error()
            )
        );

        $validate->messageWithField(['name' => ['min_length' => '{field} hello world {rule}']]);

        $error = <<<'eot'
array (
  'name' => 
  array (
    0 => '用户名 hello world 20',
  ),
)
eot;
        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());

        $this->assertSame(
            $error,
            $this->varExport(
                $validate->error()
            )
        );
    }

    public function testName()
    {
        $validate = new Validate(
            [
                'name' => '中国',
            ],
            [
                'name'     => 'required|min_length:20',
            ],
            [
                'name'     => '用户名',
            ]
        );

        $error = <<<'eot'
array (
  'name' => 
  array (
    0 => '用户名 不满足最小长度 20',
  ),
)
eot;
        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());
        $this->assertSame(['name' => '用户名'], $validate->getName());

        $this->assertSame(
            $error,
            $this->varExport(
                $validate->error()
            )
        );

        $validate->name(['name' => 'username']);

        $error = <<<'eot'
array (
  'name' => 
  array (
    0 => 'username 不满足最小长度 20',
  ),
)
eot;
        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());

        $this->assertSame(
            $error,
            $this->varExport(
                $validate->error()
            )
        );

        $validate->addName(['name' => 'hello world']);

        $error = <<<'eot'
array (
  'name' => 
  array (
    0 => 'hello world 不满足最小长度 20',
  ),
)
eot;
        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());

        $this->assertSame(
            $error,
            $this->varExport(
                $validate->error()
            )
        );
    }
}
