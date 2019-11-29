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
 * (c) 2010-2019 http://queryphp.com All rights reserved.
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
 *
 * @api(
 *     title="系统配置",
 *     path="component/option",
 *     description="
 * QueryPHP 为系统提供了灵活的配置，通常来说通过服务提供者将配置打包到服务容器中，可以很方便地使用。
 *
 * ### 使用方式
 *
 * 使用助手函数
 *
 * ``` php
 * \Leevel\Option\Helper::option_get(string $key, $default = null);
 * \Leevel\Option\Helper::option_set($name, $value = null): void;
 * \Leevel\Option\Helper::function option(): \Leevel\Option\IOption;
 * ```
 *
 * 使用容器 option 服务
 *
 * ``` php
 * \App::make('option')->set($name, $value = null): void;
 * \App::make('option')->get(string $name = 'app\\', $defaults = null);
 * ```
 *
 * 依赖注入
 *
 * ``` php
 * class Demo
 * {
 *     private $option;
 *
 *     public function __construct(\Leevel\Option\IOption $option)
 *     {
 *         $this->option = $option;
 *     }
 * }
 * ```
 *
 * ### 配置目录
 *
 * 系统配置文件为 option 目录，每个配置文件对应不同的组件，当然你也可以增加自定义的配置文件。
 *
 *  * 配置位于 `option`，可以定义配置文件。
 *  * 主要配置文件包含应用、数据库、缓存、日志、Session 等等。
 *  * 扩展配置 `common/ui/option/test.php` 目录，在 `composer.json` 中定义。
 *
 * composer.json 可以扩展目录
 *
 * ``` json
 * {
 *     "extra": {
 *         "leevel": {
 *             "@options": "The extend options",
 *             "options": {
 *                 "test": "common/ui/option/test.php"
 *             }
 *         }
 *     }
 * }
 *
 * 注意，其它软件包也可以采用这种方式自动注入扩展默认配置。
 *
 * 系统默认常见配置：
 *
 * |支持字符|替换字符|
 * |:-|:-|
 * |app|应用配置|
 * |auth|登陆验证|
 * |cache|缓存配置|
 * |console|控制台配置|
 * |cookie|Cookie 配置|
 * |database|数据库配置|
 * |debug|调试配置|
 * |filesystem|文件系统配置|
 * |i18n|国际化配置|
 * |log|日志配置|
 * |mail|邮件配置|
 * |protocol|Swoole 配置|
 * |session|Session 配置|
 * |throttler|限流配置|
 * |view|视图配置|
 * ```
 *
 * ### 配置缓存
 *
 * 配置支持生成缓存，通过内置的命令即可实现。
 *
 * ``` sh
 * php leevel option:cache
 * ```
 *
 * 返回结果
 *
 * ```
 * Start to cache option.
 * Option cache file /data/codes/queryphp/bootstrap/option.php cache successed.
 * ```
 *
 * ``` sh
 * php leevel option:clear
 * ```
 *
 * 返回结果
 *
 * ```
 * Start to clear cache option.
 * Option cache file /data/codes/queryphp/bootstrap/option.php cache clear successed.
 * ```
 * ",
 * )
 */
class OptionTest extends TestCase
{
    public function testAll(): void
    {
        $data = [
            'hello'       => 'world',
            'test\\child' => ['foo' => 'bar'],
        ];

        $option = new Option($data);

        $this->assertSame($option->all(), $data);
    }

    public function testGet(): void
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

        $this->assertSame('testing', $option->get('app\\environment'));
        $this->assertSame('testing', $option->get('environment'), 'Default namespace is app, so it equal app\\testing.');
        $this->assertNull($option->get('hello'), 'The default namespace is app, so it equal app\\hello');
        $this->assertNull($option->get('app\\hello'), 'The default namespace is app, so it equal app\\hello');
        $this->assertSame($option->get('hello\\'), 'world');
        $this->assertSame($option->get('hello\\*'), 'world');

        $this->assertSame([
            'environment' => 'testing',
            'debug'       => true,
        ], $option->get('app\\'));

        $this->assertSame([
            'environment' => 'testing',
            'debug'       => true,
        ], $option->get('app\\*'));

        $this->assertFalse([
            'environment' => 'testing',
            'debug'       => true,
        ] === $option->get('app'), 'The default namespace is app, so it equal app\\app');

        // namespace\sub.sub1.sub2
        $this->assertSame($option->get('cache\\time_preset.foo'), 'bar');
        $this->assertNull($option->get('cache\\time_preset.foo2'));
    }

    public function testHas(): void
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

        $this->assertTrue($option->has('app\\environment'));
        $this->assertTrue($option->has('environment'), 'Default namespace is app, so it equal app\\testing.');
        $this->assertFalse($option->has('hello'), 'The default namespace is app, so it equal app\\hello');
        $this->assertFalse($option->has('app\\hello'), 'The default namespace is app, so it equal app\\hello');
        $this->assertTrue($option->has('hello\\'));
        $this->assertTrue($option->has('hello\\*'));

        $this->assertTrue($option->has('app\\'));

        $this->assertTrue($option->has('app\\*'));

        $this->assertFalse($option->has('app'), 'The default namespace is app, so it equal app\\app');

        // namespace\sub.sub1.sub2
        $this->assertTrue($option->has('cache\\time_preset.foo'));
        $this->assertFalse($option->has('cache\\time_preset.foo2'));
    }

    public function testSet(): void
    {
        $data = [];

        $option = new Option($data);

        // set app\environment value
        $option->set('environment', 'testing');
        $this->assertSame('testing', $option->get('app\\environment'));
        $this->assertSame('testing', $option->get('environment'), 'Default namespace is app, so it equal app\\testing.');

        $this->assertNull($option->get('hello'), 'The default namespace is app, so it equal app\\hello');
        $option->set('hello', 'i am hello');
        $this->assertSame($option->get('hello'), 'i am hello', 'The default namespace is app, so it equal app\\hello');

        $this->assertSame($option->all(), [
            'app' => [
                'environment' => 'testing',
                'hello'       => 'i am hello',
            ],
        ]);

        // 当我们获取一个不存在的配置命名空间时，返回一个初始化的空数组
        // hello namespace not app\hello
        $this->assertSame($option->get('hello\\'), []);
        $this->assertSame($option->get('hello\\*'), []);

        $option->set('hello\\', ['foo' => ['sub' => 'bar']]);

        $this->assertSame($option->get('hello\\foo.sub'), 'bar');

        // namespace\sub.sub1.sub2
        $option->set('cache\\time_preset.foo', 'bar');
        $this->assertSame($option->get('cache\\time_preset.foo'), 'bar');
        $this->assertNull($option->get('cache\\time_preset.foo2'));
    }

    public function testSet2(): void
    {
        $data = [
            'hello' => 'world',
        ];

        $option = new Option();

        $option->set($data);

        $this->assertSame($data, $option->get());
    }

    public function testDelete(): void
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

        $option->delete('cache\\time_preset.foo');

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

    public function testDelete2(): void
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

        $option->delete('debug2.foo.bar');

        $this->assertSame($option->all(), $data);
    }

    public function testReset(): void
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

    public function testArrayAccess(): void
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
        $this->assertSame($option['cache\\time_preset.foo'], 'bar');

        // remove
        unset($option['cache\\time_preset.foo']);
        $this->assertNull($option['cache\\time_preset.foo']);

        // set
        $option['cache\\foo'] = 'bar';
        $this->assertSame($option['cache\\foo'], 'bar');

        // has
        $this->assertTrue(isset($option['hello\\']));
    }
}
