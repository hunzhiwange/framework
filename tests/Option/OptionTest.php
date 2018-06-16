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

namespace Tests\Option;

use Leevel\Option\Option;
use Tests\TestCase;

/**
 * option 组件测试.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.05.09
 *
 * @version 1.0
 * @coversNothing
 */
class OptionTest extends TestCase
{
    public function testAll()
    {
        $data = [
            'hello'      => 'world',
            'test\child' => ['foo' => 'bar'],
        ];

        $option = new Option($data);

        $this->assertSame($option->all(), $data);
    }

    public function testGet()
    {
        $data = [
            'app' => [
                'environment' => 'testing',
                'debug'       => true,
            ],
            'cache' => [
                'expire'      => 86400,
                'time_preset' => [
                    'foo' => 'bar',
                ],
            ],
            'hello' => 'world',
        ];

        $option = new Option($data);

        $this->assertSame('testing', $option->get('app\environment'));
        $this->assertSame('testing', $option->get('environment'), 'Default namespace is app, so it equal app\testing.');
        $this->assertNull($option->get('hello'), 'The default namespace is app, so it equal app\hello');
        $this->assertNull($option->get('app\hello'), 'The default namespace is app, so it equal app\hello');
        $this->assertSame($option->get('hello\\'), 'world');
        $this->assertSame($option->get('hello\*'), 'world');

        $this->assertSame([
            'environment' => 'testing',
            'debug'       => true,
        ], $option->get('app\\'));

        $this->assertSame([
            'environment' => 'testing',
            'debug'       => true,
        ], $option->get('app\*'));

        $this->assertFalse([
            'environment' => 'testing',
            'debug'       => true,
        ] === $option->get('app'), 'The default namespace is app, so it equal app\app');

        // namespace\sub.sub1.sub2
        $this->assertSame($option->get('cache\time_preset.foo'), 'bar');
        $this->assertNull($option->get('cache\time_preset.foo2'));
    }

    public function testHas()
    {
        $data = [
            'app' => [
                'environment' => 'testing',
                'debug'       => true,
            ],
            'cache' => [
                'expire'      => 86400,
                'time_preset' => [
                    'foo' => 'bar',
                ],
            ],
            'hello' => 'world',
        ];

        $option = new Option($data);

        $this->assertTrue($option->has('app\environment'));
        $this->assertTrue($option->has('environment'), 'Default namespace is app, so it equal app\testing.');
        $this->assertFalse($option->has('hello'), 'The default namespace is app, so it equal app\hello');
        $this->assertFalse($option->has('app\hello'), 'The default namespace is app, so it equal app\hello');
        $this->assertTrue($option->has('hello\\'));
        $this->assertTrue($option->has('hello\*'));

        $this->assertTrue($option->has('app\\'));

        $this->assertTrue($option->has('app\*'));

        $this->assertFalse($option->has('app'), 'The default namespace is app, so it equal app\app');

        // namespace\sub.sub1.sub2
        $this->assertTrue($option->has('cache\time_preset.foo'));
        $this->assertFalse($option->has('cache\time_preset.foo2'));
    }

    public function testSet()
    {
        $data = [];

        $option = new Option($data);

        // set app\environment value
        $option->set('environment', 'testing');
        $this->assertSame('testing', $option->get('app\environment'));
        $this->assertSame('testing', $option->get('environment'), 'Default namespace is app, so it equal app\testing.');

        $this->assertNull($option->get('hello'), 'The default namespace is app, so it equal app\hello');
        $option->set('hello', 'i am hello');
        $this->assertSame($option->get('hello'), 'i am hello', 'The default namespace is app, so it equal app\hello');

        $this->assertSame($option->all(), [
            'app' => [
                'environment' => 'testing',
                'hello'       => 'i am hello',
            ],
        ]);

        // 当我们获取一个不存在的配置命名空间时，返回一个初始化的空数组
        // hello namespace not app\hello
        $this->assertSame($option->get('hello\\'), []);
        $this->assertSame($option->get('hello\*'), []);

        $option->set('hello\\', ['foo' => ['sub' => 'bar']]);

        $this->assertSame($option->get('hello\foo.sub'), 'bar');

        // namespace\sub.sub1.sub2
        $option->set('cache\time_preset.foo', 'bar');
        $this->assertSame($option->get('cache\time_preset.foo'), 'bar');
        $this->assertNull($option->get('cache\time_preset.foo2'));
    }

    public function testDelete()
    {
        $data = [
            'app' => [
                'environment' => 'testing',
                'debug'       => true,
            ],
            'cache' => [
                'expire'      => 86400,
                'time_preset' => [
                    'foo' => 'bar',
                ],
            ],
            'hello' => 'world',
        ];

        $option = new Option($data);

        $option->delete('debug');

        $this->assertSame($option->all(), [
            'app' => [
                'environment' => 'testing',
            ],
            'cache' => [
                'expire'      => 86400,
                'time_preset' => [
                    'foo' => 'bar',
                ],
            ],
            'hello' => 'world',
        ]);

        $option->delete('cache\time_preset.foo');

        $this->assertSame($option->all(), [
            'app' => [
                'environment' => 'testing',
            ],
            'cache' => [
                'expire'      => 86400,
                'time_preset' => [
                ],
            ],
            'hello' => 'world',
        ]);

        // 删除命令空间会初始化该命名空间为空数组，不存在会创建一个空数组
        $option->delete('hello\\');

        $this->assertSame($option->all(), [
            'app' => [
                'environment' => 'testing',
            ],
            'cache' => [
                'expire'      => 86400,
                'time_preset' => [
                ],
            ],
            'hello' => [],
        ]);

        $option->delete('world\\');

        $this->assertSame($option->all(), [
            'app' => [
                'environment' => 'testing',
            ],
            'cache' => [
                'expire'      => 86400,
                'time_preset' => [
                ],
            ],
            'hello' => [],
            'world' => [],
        ]);
    }

    public function testReset()
    {
        $data = [
            'hello' => 'world',
        ];

        $option = new Option($data);

        $this->assertSame($option->all(), [
            'hello' => 'world',
        ]);

        // array
        $option->reset(['foo' => 'bar']);
        $this->assertSame($option->all(), [
            'foo' => 'bar',
        ]);

        // set a namespace
        $option->reset('foo');
        $this->assertSame($option->all(), [
            'foo' => [],
        ]);

        $option->reset('foo2');
        $this->assertSame($option->all(), [
            'foo'  => [],
            'foo2' => [],
        ]);

        // reset all
        $option->reset();
        $this->assertSame($option->all(), []);
    }

    public function testArrayAccess()
    {
        $data = [
            'app' => [
                'environment' => 'testing',
                'debug'       => true,
            ],
            'cache' => [
                'expire'      => 86400,
                'time_preset' => [
                    'foo' => 'bar',
                ],
            ],
            'hello' => 'world',
        ];

        $option = new Option($data);

        // get
        $this->assertSame($option['cache\time_preset.foo'], 'bar');

        // remove
        unset($option['cache\time_preset.foo']);
        $this->assertNull($option['cache\time_preset.foo']);

        // set
        $option['cache\foo'] = 'bar';
        $this->assertSame($option['cache\foo'], 'bar');

        // has
        $this->assertTrue(isset($option['hello\\']));
    }
}
