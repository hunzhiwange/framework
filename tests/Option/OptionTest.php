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

namespace Tests\Option;

use Leevel\Option\Option;
use Tests\TestCase;

/**
 * option 组件测试.
 *
 * @api(
 *     title="系统配置",
 *     path="component/option",
 *     description="
 * QueryPHP 为系统提供了灵活的配置，通常来说通过服务提供者将配置打包到服务容器中，可以很方便地使用。
 *
 * ## 使用方式
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
 * 使用静态代理
 *
 * ``` php
 * \Leevel\Option\Proxy\Option::set($name, $value = null): void;
 * \Leevel\Option\Proxy\Option::get(string $name = 'app\\', $defaults = null);
 * ```
 *
 * ## 配置目录
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
 * ```
 *
 * ::: warning
 * 注意，其它软件包也可以采用这种方式自动注入扩展默认配置。
 * :::
 *
 * 系统默认常见配置：
 *
 * |配置项|配置描述|
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
 *
 * ## 配置缓存
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
 * 清理配置缓存
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
 *
 * ## 配置定义
 *
 * 可以直接在相应的配置文件已数组的方式定义，新的配置文件直接放入目录即可。
 *
 * ::: tip
 * 配置参数名严格区分大小写，建议是使用小写定义配置参数的规范。
 * :::
 *
 * app 应用配置中几个核心的配置项，这是整个系统关键的配置。
 *
 * | 配置项 | 配置值 | 描述  |
 * | :- | :- | :- |
 * | environment |  development | 运行环境，可以为 production : 生产环境 testing : 测试环境 development : 开发环境 |
 * | debug  | true | 是否打开调试模式，可以为 true : 开启调试 false 关闭调试，打开调试模式可以显示更多精确的错误信息。  |
 * | auth_key  | 7becb888f518b20224a988906df51e05  | 安全 key，请妥善保管此安全 key,防止密码被人破解。 |
 *
 * ## 环境变量定义
 *
 * 可以在应用的根目录下定义一个特殊的 `.env` 环境变量文件，一般用于平时开发使用。
 *
 * ### 自定义环境变量
 *
 * 可以通过 `RUNTIME_ENVIRONMENT` 来定义自定义的环境变量文件，比如定义 `.test` 的环境变量。
 *
 * ``` php
 * putenv('RUNTIME_ENVIRONMENT=test');
 * ```
 *
 * 环境变量配置格式
 *
 * ```
 * // Environment production、testing and development
 * ENVIRONMENT = development
 *
 * // Debug
 * DEBUG = true
 * DEBUG_JSON = true
 * DEBUG_CONSOLE = true
 * DEBUG_JAVASCRIPT = true
 *
 * ...
 * ```
 *
 * 获取环境配置
 *
 * ``` php
 * \env('environment');
 * \Leevel::env('environment');
 * \App::env('environment');
 * ```
 * ",
 * )
 */
class OptionTest extends TestCase
{
    /**
     * @api(
     *     title="all 返回所有配置",
     *     description="",
     *     note="",
     * )
     */
    public function testAll(): void
    {
        $data = [
            'hello'       => 'world',
            'test\\child' => ['foo' => 'bar'],
        ];

        $option = new Option($data);

        $this->assertSame($option->all(), $data);
    }

    /**
     * @api(
     *     title="get 获取配置",
     *     description="",
     *     note="",
     * )
     */
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

    /**
     * @api(
     *     title="has 是否存在配置",
     *     description="",
     *     note="",
     * )
     */
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

    /**
     * @api(
     *     title="set 设置配置",
     *     description="",
     *     note="",
     * )
     */
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

    /**
     * @api(
     *     title="delete 删除配置",
     *     description="",
     *     note="",
     * )
     */
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

    /**
     * @api(
     *     title="reset 重置配置",
     *     description="危险操作，一般没有必要调用。",
     *     note="",
     * )
     */
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

    /**
     * @api(
     *     title="数组访问配置对象",
     *     description="配置实现了 `\ArrayAccess`，可以通过以数组的方式访问配置对象，在服务提供者中经常运用。",
     *     note="",
     * )
     */
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
