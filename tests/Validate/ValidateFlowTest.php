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

use Leevel\Validate\Validate;
use Tests\TestCase;

/**
 * validateFlow test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.08.12
 *
 * @version 1.0
 */
class ValidateFlowTest extends TestCase
{
    public function testData()
    {
        $validate = $this->makeBaseValidate();

        $condition = false;

        $validate->

        ifs($condition)->

        data(['name' => 'foo'])->

        elses()->

        data(['name' => 'bar'])->

        endIfs();

        $this->assertTrue($validate->success());
        $this->assertFalse($validate->fail());
        $this->assertSame(['name' => 'bar'], $validate->getData());
    }

    public function testData2()
    {
        $validate = $this->makeBaseValidate();

        $condition = true;

        $validate->

        ifs($condition)->

        data(['name' => 'foo'])->

        elses()->

        data(['name' => 'bar'])->

        endIfs();

        $this->assertTrue($validate->success());
        $this->assertFalse($validate->fail());
        $this->assertSame(['name' => 'foo'], $validate->getData());
    }

    public function testAddData()
    {
        $validate = $this->makeBaseValidate();

        $condition = false;

        $validate->

        ifs($condition)->

        addData(['name' => 'foo'])->

        elses()->

        addData(['name' => 'bar'])->

        endIfs();

        $this->assertTrue($validate->success());
        $this->assertFalse($validate->fail());
        $this->assertSame(['name' => 'bar'], $validate->getData());
    }

    public function testAddData2()
    {
        $validate = $this->makeBaseValidate();

        $condition = true;

        $validate->

        ifs($condition)->

        addData(['name' => 'foo'])->

        elses()->

        addData(['name' => 'bar'])->

        endIfs();

        $this->assertTrue($validate->success());
        $this->assertFalse($validate->fail());
        $this->assertSame(['name' => 'foo'], $validate->getData());
    }

    public function testRule()
    {
        $validate = $this->makeBaseValidate();

        $condition = false;

        $validate->

        ifs($condition)->

        rule(['name' => 'required|max_length:9'])->

        elses()->

        rule(['name' => 'required|max_length:2'])->

        endIfs();

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());
        $this->assertSame(['name' => '小牛神'], $validate->getData());
    }

    public function testRule2()
    {
        $validate = $this->makeBaseValidate();

        $condition = true;

        $validate->

        ifs($condition)->

        rule(['name' => 'required|max_length:9'])->

        elses()->

        rule(['name' => 'required|max_length:2'])->

        endIfs();

        $this->assertTrue($validate->success());
        $this->assertFalse($validate->fail());
        $this->assertSame(['name' => '小牛神'], $validate->getData());
    }

    public function testRuleIf()
    {
        $validate = $this->makeBaseValidate();

        $condition = false;

        $validate->

        ifs($condition)->

        rule(['name' => 'required|max_length:9'], function (array $data) {
            $this->assertSame(['name' => '小牛神'], $data);

            return true;
        })->

        elses()->

        rule(['name' => 'required|max_length:2'], function (array $data) {
            $this->assertSame(['name' => '小牛神'], $data);

            return true;
        })->

        endIfs();

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());
        $this->assertSame(['name' => '小牛神'], $validate->getData());
    }

    public function testRuleIf2()
    {
        $validate = $this->makeBaseValidate();

        $condition = true;

        $validate->

        ifs($condition)->

        rule(['name' => 'required|max_length:9'], function (array $data) {
            $this->assertSame(['name' => '小牛神'], $data);

            return true;
        })->

        elses()->

        rule(['name' => 'required|max_length:2'], function (array $data) {
            $this->assertSame(['name' => '小牛神'], $data);

            return true;
        })->

        endIfs();

        $this->assertTrue($validate->success());
        $this->assertFalse($validate->fail());
        $this->assertSame(['name' => '小牛神'], $validate->getData());
    }

    public function testAddRule()
    {
        $validate = $this->makeBaseValidate();

        $condition = false;

        $validate->

        ifs($condition)->

        addRule(['name' => 'required|max_length:9'])->

        elses()->

        addRule(['name' => 'required|max_length:2'])->

        endIfs();

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());
        $this->assertSame(['name' => '小牛神'], $validate->getData());
    }

    public function testAddRule2()
    {
        $validate = $this->makeBaseValidate();

        $condition = true;

        $validate->

        ifs($condition)->

        addRule(['name' => 'required|max_length:9'])->

        elses()->

        addRule(['name' => 'required|max_length:2'])->

        endIfs();

        $this->assertTrue($validate->success());
        $this->assertFalse($validate->fail());
        $this->assertSame(['name' => '小牛神'], $validate->getData());
    }

    public function testAddRuleIf()
    {
        $validate = $this->makeBaseValidate();

        $condition = false;

        $validate->

        ifs($condition)->

        addRule(['name' => 'required|max_length:9'], function (array $data) {
            $this->assertSame(['name' => '小牛神'], $data);

            return true;
        })->

        elses()->

        addRule(['name' => 'required|max_length:2'], function (array $data) {
            $this->assertSame(['name' => '小牛神'], $data);

            return true;
        })->

        endIfs();

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());
        $this->assertSame(['name' => '小牛神'], $validate->getData());
    }

    public function testAddRuleIf2()
    {
        $validate = $this->makeBaseValidate();

        $condition = true;

        $validate->

        ifs($condition)->

        addRule(['name' => 'required|max_length:9'], function (array $data) {
            $this->assertSame(['name' => '小牛神'], $data);

            return true;
        })->

        elses()->

        addRule(['name' => 'required|max_length:2'], function (array $data) {
            $this->assertSame(['name' => '小牛神'], $data);

            return true;
        })->

        endIfs();

        $this->assertTrue($validate->success());
        $this->assertFalse($validate->fail());
        $this->assertSame(['name' => '小牛神'], $validate->getData());
    }

    public function testMessage()
    {
        $validate = $this->makeBaseValidate();

        $validate->rule(['name' => 'required|min_length:9']);

        $condition = false;

        $validate->

        ifs($condition)->

        message(['min_length' => '{field} foo min {rule}'])->

        elses()->

        message(['min_length' => '{field} bar min {rule}'])->

        endIfs();

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());
        $this->assertSame(['name' => '小牛神'], $validate->getData());

        $error = <<<'eot'
array (
  'name' => 
  array (
    0 => '用户名 bar min 9',
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

    public function testMessage2()
    {
        $validate = $this->makeBaseValidate();

        $validate->rule(['name' => 'required|min_length:9']);

        $condition = true;

        $validate->

        ifs($condition)->

        message(['min_length' => '{field} foo min {rule}'])->

        elses()->

        message(['min_length' => '{field} bar min {rule}'])->

        endIfs();

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());
        $this->assertSame(['name' => '小牛神'], $validate->getData());

        $error = <<<'eot'
array (
  'name' => 
  array (
    0 => '用户名 foo min 9',
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

    public function testAddMessage()
    {
        $validate = $this->makeBaseValidate();

        $validate->rule(['name' => 'required|min_length:9']);

        $condition = false;

        $validate->

        ifs($condition)->

        addMessage(['min_length' => '{field} foo min {rule}'])->

        elses()->

        addMessage(['min_length' => '{field} bar min {rule}'])->

        endIfs();

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());
        $this->assertSame(['name' => '小牛神'], $validate->getData());

        $error = <<<'eot'
array (
  'name' => 
  array (
    0 => '用户名 bar min 9',
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

    public function testAddMessage2()
    {
        $validate = $this->makeBaseValidate();

        $validate->rule(['name' => 'required|min_length:9']);

        $condition = true;

        $validate->

        ifs($condition)->

        addMessage(['min_length' => '{field} foo min {rule}'])->

        elses()->

        addMessage(['min_length' => '{field} bar min {rule}'])->

        endIfs();

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());
        $this->assertSame(['name' => '小牛神'], $validate->getData());

        $error = <<<'eot'
array (
  'name' => 
  array (
    0 => '用户名 foo min 9',
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

    public function testMessageWithField()
    {
        $validate = $this->makeBaseValidate();

        $validate->rule(['name' => 'required|min_length:9']);

        $condition = false;

        $validate->

        ifs($condition)->

        messageWithField(['name' => ['min_length' => '{field} hello foo {rule}']])->

        elses()->

        messageWithField(['name' => ['min_length' => '{field} hello bar {rule}']])->

        endIfs();

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());
        $this->assertSame(['name' => '小牛神'], $validate->getData());

        $error = <<<'eot'
array (
  'name' => 
  array (
    0 => '用户名 hello bar 9',
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

    public function testMessageWithField2()
    {
        $validate = $this->makeBaseValidate();

        $validate->rule(['name' => 'required|min_length:9']);

        $condition = true;

        $validate->

        ifs($condition)->

        messageWithField(['name' => ['min_length' => '{field} hello foo {rule}']])->

        elses()->

        messageWithField(['name' => ['min_length' => '{field} hello bar {rule}']])->

        endIfs();

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());
        $this->assertSame(['name' => '小牛神'], $validate->getData());

        $error = <<<'eot'
array (
  'name' => 
  array (
    0 => '用户名 hello foo 9',
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

    public function testName()
    {
        $validate = $this->makeBaseValidate();

        $validate->rule(['name' => 'required|min_length:9']);

        $condition = false;

        $validate->

        ifs($condition)->

        name(['name' => 'foo'])->

        elses()->

        name(['name' => 'bar'])->

        endIfs();

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());
        $this->assertSame(['name' => '小牛神'], $validate->getData());

        $error = <<<'eot'
array (
  'name' => 
  array (
    0 => 'bar 不满足最小长度 9',
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

    public function testName2()
    {
        $validate = $this->makeBaseValidate();

        $validate->rule(['name' => 'required|min_length:9']);

        $condition = true;

        $validate->

        ifs($condition)->

        name(['name' => 'foo'])->

        elses()->

        name(['name' => 'bar'])->

        endIfs();

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());
        $this->assertSame(['name' => '小牛神'], $validate->getData());

        $error = <<<'eot'
array (
  'name' => 
  array (
    0 => 'foo 不满足最小长度 9',
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

    public function testAddName()
    {
        $validate = $this->makeBaseValidate();

        $validate->rule(['name' => 'required|min_length:9']);

        $condition = false;

        $validate->

        ifs($condition)->

        addName(['name' => 'foo'])->

        elses()->

        addName(['name' => 'bar'])->

        endIfs();

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());
        $this->assertSame(['name' => '小牛神'], $validate->getData());

        $error = <<<'eot'
array (
  'name' => 
  array (
    0 => 'bar 不满足最小长度 9',
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

    public function testAddName2()
    {
        $validate = $this->makeBaseValidate();

        $validate->rule(['name' => 'required|min_length:9']);

        $condition = true;

        $validate->

        ifs($condition)->

        addName(['name' => 'foo'])->

        elses()->

        addName(['name' => 'bar'])->

        endIfs();

        $this->assertFalse($validate->success());
        $this->assertTrue($validate->fail());
        $this->assertSame(['name' => '小牛神'], $validate->getData());

        $error = <<<'eot'
array (
  'name' => 
  array (
    0 => 'foo 不满足最小长度 9',
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

    protected function makeBaseValidate(): Validate
    {
        $validate = new Validate(
            [
                'name' => '小牛神',
            ],
            [
                'name'     => 'required|max_length:10',
            ],
            [
                'name'     => '用户名',
            ]
        );

        $this->assertTrue($validate->success());
        $this->assertFalse($validate->fail());
        $this->assertSame([], $validate->error());
        $this->assertSame([], $validate->getMessage());
        $this->assertSame(['name' => '小牛神'], $validate->getData());

        return $validate;
    }
}
