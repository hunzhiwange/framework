<?php

declare(strict_types=1);

namespace Tests\Config;

use Leevel\Config\Config;
use Leevel\Kernel\Utils\Api;
use Tests\TestCase;

#[Api([
    'zh-CN:title' => '系统配置',
    'path' => 'component/config',
    'zh-CN:description' => <<<'EOT'
QueryPHP 为系统提供了灵活的配置，通常来说通过服务提供者将配置打包到服务容器中，可以很方便地使用。

## 使用方式

使用容器 config 服务

``` php
\App::make('config')->set($name, $value = null): void;
\App::make('config')->get(string $name = 'app\\', $defaults = null);
```

依赖注入

``` php
class Demo
{
    private \Leevel\Config\IConfig $config;

    public function __construct(\Leevel\Config\IConfig $config)
    {
        $this->config = $config;
    }
}
```

使用静态代理

``` php
\Leevel\Config\Proxy\Config::set($name, $value = null): void;
\Leevel\Config\Proxy\Config::get(string $name = 'app\\', $defaults = null);
```

## 配置目录

系统配置文件为 config 目录，每个配置文件对应不同的组件，当然你也可以增加自定义的配置文件。

 * 配置位于 `config`，可以定义配置文件。
 * 主要配置文件包含应用、数据库、缓存、日志、Session 等等。
 * 扩展配置 `common/ui/config/test.php` 目录，在 `composer.json` 中定义。

composer.json 可以扩展目录

``` json
{
    "extra": {
        "leevel": {
            "@configs": "The extend configs",
            "configs": {
                "test": "common/ui/config/test.php"
            }
        }
    }
}
```

::: warning
注意，其它软件包也可以采用这种方式自动注入扩展默认配置。
:::

系统默认常见配置：

|配置项|配置描述|
|:-|:-|
|app|应用配置|
|auth|登陆验证|
|cache|缓存配置|
|console|控制台配置|
|cookie|Cookie 配置|
|database|数据库配置|
|debug|调试配置|
|filesystem|文件系统配置|
|i18n|国际化配置|
|log|日志配置|
|session|Session 配置|
|view|视图配置|

## 配置缓存

配置支持生成缓存，通过内置的命令即可实现。

``` sh
php leevel config:cache
```

返回结果

```
Start to cache config.
Config cache file /data/codes/queryphp/bootstrap/config.php cache succeed.
```

清理配置缓存

``` sh
php leevel config:clear
```

返回结果

```
Start to clear cache config.
Config cache file /data/codes/queryphp/bootstrap/config.php cache clear succeed.
```

## 配置定义

可以直接在相应的配置文件已数组的方式定义，新的配置文件直接放入目录即可。

::: tip
配置参数名严格区分大小写，建议是使用小写定义配置参数的规范。
:::

app 应用配置中几个核心的配置项，这是整个系统关键的配置。

| 配置项 | 配置值 | 描述  |
| :- | :- | :- |
| environment |  development | 运行环境，可以为 production : 生产环境 testing : 测试环境 development : 开发环境 |
| debug  | true | 是否打开调试模式，可以为 true : 开启调试 false 关闭调试，打开调试模式可以显示更多精确的错误信息。  |
| auth_key  | 7becb888f518b20224a988906df51e05  | 安全 key，请妥善保管此安全 key,防止密码被人破解。 |

## 环境变量定义

可以在应用的根目录下定义一个特殊的 `.env` 环境变量文件，一般用于平时开发使用。

### 自定义环境变量

可以通过 `RUNTIME_ENVIRONMENT` 来定义自定义的环境变量文件，比如定义 `.test` 的环境变量。

``` php
putenv('RUNTIME_ENVIRONMENT=test');
```

环境变量配置格式

```
# Environment production、testing and development
ENVIRONMENT = development

# Debug
DEBUG = true
DEBUG_JSON = true
DEBUG_CONSOLE = true
DEBUG_JAVASCRIPT = true

...
```

获取环境配置

``` php
\env('environment');
\Leevel::env('environment');
\App::env('environment');
```
EOT,
])]
final class ConfigTest extends TestCase
{
    #[Api([
        'zh-CN:title' => 'all 返回所有配置',
    ])]
    public function testAll(): void
    {
        $data = [
            'hello' => 'world',
            'test\\child' => ['foo' => 'bar'],
        ];

        $config = new Config($data);

        static::assertSame($config->all(), $data);
    }

    #[Api([
        'zh-CN:title' => 'get 获取配置',
    ])]
    public function testGet(): void
    {
        $data = [
            'app' => [
                'environment' => 'testing',
                'debug' => true,
            ],
            'cache' => [
                'expire' => 86400,
                'time_preset' => [
                    'foo' => 'bar',
                ],
            ],
            'hello' => 'world',
        ];

        $config = new Config($data);

        static::assertSame('testing', $config->get('app\\environment'));
        static::assertSame('testing', $config->get('environment'), 'Default namespace is app, so it equal app\\testing.');
        static::assertNull($config->get('hello'), 'The default namespace is app, so it equal app\\hello');
        static::assertNull($config->get('app\\hello'), 'The default namespace is app, so it equal app\\hello');
        static::assertSame($config->get('hello\\'), 'world');
        static::assertSame($config->get('hello\\*'), 'world');

        static::assertSame([
            'environment' => 'testing',
            'debug' => true,
        ], $config->get('app\\'));

        static::assertSame([
            'environment' => 'testing',
            'debug' => true,
        ], $config->get('app\\*'));

        static::assertFalse([
            'environment' => 'testing',
            'debug' => true,
        ] === $config->get('app'), 'The default namespace is app, so it equal app\\app');

        // namespace\sub.sub1.sub2
        static::assertSame($config->get('cache\\time_preset.foo'), 'bar');
        static::assertNull($config->get('cache\\time_preset.foo2'));
    }

    #[Api([
        'zh-CN:title' => 'has 是否存在配置',
    ])]
    public function testHas(): void
    {
        $data = [
            'app' => [
                'environment' => 'testing',
                'debug' => true,
            ],
            'cache' => [
                'expire' => 86400,
                'time_preset' => [
                    'foo' => 'bar',
                ],
            ],
            'hello' => 'world',
        ];

        $config = new Config($data);

        static::assertTrue($config->has('app\\environment'));
        static::assertTrue($config->has('environment'), 'Default namespace is app, so it equal app\\testing.');
        static::assertFalse($config->has('hello'), 'The default namespace is app, so it equal app\\hello');
        static::assertFalse($config->has('app\\hello'), 'The default namespace is app, so it equal app\\hello');
        static::assertTrue($config->has('hello\\'));
        static::assertTrue($config->has('hello\\*'));
        static::assertTrue($config->has('app\\'));
        static::assertTrue($config->has('app\\*'));
        static::assertFalse($config->has('app'), 'The default namespace is app, so it equal app\\app');

        // namespace\sub.sub1.sub2
        static::assertTrue($config->has('cache\\time_preset.foo'));
        static::assertFalse($config->has('cache\\time_preset.foo2'));
    }

    #[Api([
        'zh-CN:title' => 'set 设置配置',
    ])]
    public function testSet(): void
    {
        $data = [];

        $config = new Config($data);

        // set app\environment value
        $config->set('environment', 'testing');
        static::assertSame('testing', $config->get('app\\environment'));
        static::assertSame('testing', $config->get('environment'), 'Default namespace is app, so it equal app\\testing.');

        static::assertNull($config->get('hello'), 'The default namespace is app, so it equal app\\hello');
        $config->set('hello', 'i am hello');
        static::assertSame($config->get('hello'), 'i am hello', 'The default namespace is app, so it equal app\\hello');

        static::assertSame($config->all(), [
            'app' => [
                'environment' => 'testing',
                'hello' => 'i am hello',
            ],
        ]);

        // 当我们获取一个不存在的配置命名空间时，返回一个初始化的空数组
        // hello namespace not app\hello
        static::assertSame($config->get('hello\\'), []);
        static::assertSame($config->get('hello\\*'), []);

        $config->set('hello\\', ['foo' => ['sub' => 'bar']]);

        static::assertSame($config->get('hello\\foo.sub'), 'bar');

        // namespace\sub.sub1.sub2
        $config->set('cache\\time_preset.foo', 'bar');
        static::assertSame($config->get('cache\\time_preset.foo'), 'bar');
        static::assertNull($config->get('cache\\time_preset.foo2'));
    }

    public function testSet2(): void
    {
        $data = [
            'hello' => 'world',
        ];

        $config = new Config();
        $config->set($data);

        static::assertSame($data, $config->get());
    }

    #[Api([
        'zh-CN:title' => 'delete 删除配置',
    ])]
    public function testDelete(): void
    {
        $data = [
            'app' => [
                'environment' => 'testing',
                'debug' => true,
            ],
            'cache' => [
                'expire' => 86400,
                'time_preset' => [
                    'foo' => 'bar',
                ],
            ],
            'hello' => 'world',
        ];

        $config = new Config($data);
        $config->delete('debug');

        static::assertSame($config->all(), [
            'app' => [
                'environment' => 'testing',
            ],
            'cache' => [
                'expire' => 86400,
                'time_preset' => [
                    'foo' => 'bar',
                ],
            ],
            'hello' => 'world',
        ]);

        $config->delete('cache\\time_preset.foo');

        static::assertSame($config->all(), [
            'app' => [
                'environment' => 'testing',
            ],
            'cache' => [
                'expire' => 86400,
                'time_preset' => [
                ],
            ],
            'hello' => 'world',
        ]);

        // 删除命令空间会初始化该命名空间为空数组，不存在会创建一个空数组
        $config->delete('hello\\');

        static::assertSame($config->all(), [
            'app' => [
                'environment' => 'testing',
            ],
            'cache' => [
                'expire' => 86400,
                'time_preset' => [
                ],
            ],
            'hello' => [],
        ]);

        $config->delete('world\\');

        static::assertSame($config->all(), [
            'app' => [
                'environment' => 'testing',
            ],
            'cache' => [
                'expire' => 86400,
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
                'debug' => true,
            ],
            'cache' => [
                'expire' => 86400,
                'time_preset' => [
                    'foo' => 'bar',
                ],
            ],
            'hello' => 'world',
        ];

        $config = new Config($data);
        $config->delete('debug2.foo.bar');

        static::assertSame($config->all(), $data);
    }

    #[Api([
        'zh-CN:title' => 'reset 重置配置',
        'zh-CN:description' => <<<'EOT'
危险操作，一般没有必要调用。
EOT,
    ])]
    public function testReset(): void
    {
        $data = [
            'hello' => 'world',
        ];

        $config = new Config($data);

        static::assertSame($config->all(), [
            'hello' => 'world',
        ]);

        // array
        $config->reset(['foo' => 'bar']);
        static::assertSame($config->all(), [
            'foo' => 'bar',
        ]);

        // set a namespace
        $config->reset('foo');
        static::assertSame($config->all(), [
            'foo' => [],
        ]);

        $config->reset('foo2');
        static::assertSame($config->all(), [
            'foo' => [],
            'foo2' => [],
        ]);

        // reset all
        $config->reset();
        static::assertSame($config->all(), []);
    }

    #[Api([
        'zh-CN:title' => '数组访问配置对象',
        'zh-CN:description' => <<<'EOT'
配置实现了 `\ArrayAccess`，可以通过以数组的方式访问配置对象，在服务提供者中经常运用。
EOT,
    ])]
    public function testArrayAccess(): void
    {
        $data = [
            'app' => [
                'environment' => 'testing',
                'debug' => true,
            ],
            'cache' => [
                'expire' => 86400,
                'time_preset' => [
                    'foo' => 'bar',
                ],
            ],
            'hello' => 'world',
        ];

        $config = new Config($data);

        // get
        static::assertSame($config['cache\\time_preset.foo'], 'bar');

        // remove
        unset($config['cache\\time_preset.foo']);
        static::assertNull($config['cache\\time_preset.foo']);

        // set
        $config['cache\\foo'] = 'bar';
        static::assertSame($config['cache\\foo'], 'bar');

        // has
        static::assertTrue(isset($config['hello\\']));
    }
}
