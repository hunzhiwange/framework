<?php

declare(strict_types=1);

namespace Tests\Config;

use Leevel\Config\ComposerConfig;
use Leevel\Kernel\Utils\Api;
use Tests\TestCase;

#[Api([
    'zh-CN:title' => 'Composer 扩展配置',
    'path' => 'component/config/composer',
    'zh-CN:description' => <<<'EOT'
QueryPHP 系统服务提供者、应用命令、扩展配置和扩展语言包等都在 `composer` 中进行定义。
EOT,
])]
final class ComposerConfigTest extends TestCase
{
    #[Api([
        'zh-CN:title' => 'composer.json',
        'zh-CN:description' => <<<'EOT'
示例配置

``` json
{[file_get_contents('vendor/hunzhiwange/framework/tests/Config/app1/composer.json')]}
```

相关配置约定在 `leevel` 字段中，可以非常方便地扩展系统。
EOT,
    ])]
    public function testBaseUse(): void
    {
        $configs = ($composerConfig = new ComposerConfig(__DIR__.'/app1'))->loadData();

        $data = <<<'eot'
            {
                "providers": [
                    "Tests\\Config\\Providers\\Foo",
                    "Tests\\Config\\Providers\\Bar",
                    "Demo\\Provider\\Register",
                    "Common\\Infra\\Provider\\Event",
                    "Common\\Infra\\Provider\\Router"
                ],
                "ignores": [
                    "Leevel\\Notexits\\Provider\\Register"
                ],
                "commands": [
                    "Tests\\Config\\Commands\\Test",
                    "Tests\\Config\\Commands\\Console",
                    "Demo\\Demo\\Console",
                    "Common\\App\\Console"
                ],
                "configs": {
                    "demo": "config\/extend\/test.php"
                },
                "i18ns": [
                    "i18n\/extend"
                ],
                "i18n-paths": [],
                "metas": {
                    "foo": "bar"
                }
            }
            eot;

        static::assertSame(
            $data,
            $this->varJson(
                $configs
            )
        );

        static::assertSame(
            $data,
            $this->varJson(
                $composerConfig->loadData()
            )
        );
    }

    public function test1(): void
    {
        $configs = ($composerConfig = new ComposerConfig(__DIR__.'/app8'))->loadData();

        $data = <<<'eot'
            {
                "providers": [
                    "Tests\\Config\\Providers\\Foo",
                    "Tests\\Config\\Providers\\Bar",
                    "Demo\\Provider\\Register",
                    "Common\\Infra\\Provider\\Event",
                    "Common\\Infra\\Provider\\Router"
                ],
                "ignores": [
                    "Leevel\\Notexits\\Provider\\Register"
                ],
                "commands": [
                    "Tests\\Config\\Commands\\Test",
                    "Tests\\Config\\Commands\\Console",
                    "Demo\\Demo\\Console",
                    "Common\\App\\Console"
                ],
                "configs": {
                    "demo": "config\/extend\/test.php"
                },
                "i18ns": [
                    "i18n\/extend"
                ],
                "i18n-paths": [],
                "metas": {
                    "foo": "bar"
                }
            }
            eot;

        static::assertSame(
            $data,
            $this->varJson(
                $configs
            )
        );

        static::assertSame(
            $data,
            $this->varJson(
                $composerConfig->loadData()
            )
        );
    }

    public function testComposerNotFound(): void
    {
        $configs = (new ComposerConfig(__DIR__.'/app4'))->loadData();

        $data = <<<'eot'
            {
                "providers": [
                    "Tests\\Config\\Providers\\Foo",
                    "Tests\\Config\\Providers\\Bar",
                    "Demo\\Provider\\Register"
                ],
                "ignores": [],
                "commands": [
                    "Tests\\Config\\Commands\\Test",
                    "Tests\\Config\\Commands\\Console",
                    "Demo\\Demo\\Console"
                ],
                "configs": [],
                "i18ns": [],
                "i18n-paths": [],
                "metas": []
            }
            eot;

        static::assertSame(
            $data,
            $this->varJson(
                $configs
            )
        );
    }

    public function testComposerExtraConfigNotFound(): void
    {
        $configs = (new ComposerConfig(__DIR__.'/app7'))->loadData();

        $data = <<<'eot'
            {
                "providers": [
                    "Tests\\Config\\Providers\\Foo",
                    "Tests\\Config\\Providers\\Bar",
                    "Demo\\Provider\\Register"
                ],
                "ignores": [],
                "commands": [
                    "Tests\\Config\\Commands\\Test",
                    "Tests\\Config\\Commands\\Console",
                    "Demo\\Demo\\Console"
                ],
                "configs": [],
                "i18ns": [],
                "i18n-paths": [],
                "metas": []
            }
            eot;

        static::assertSame(
            $data,
            $this->varJson(
                $configs
            )
        );
    }
}
