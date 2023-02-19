<?php

declare(strict_types=1);

namespace Tests\Option;

use Leevel\Option\ComposerOption;
use Tests\TestCase;

/**
 * @api(
 *     zh-CN:title="Composer 扩展配置",
 *     path="component/option/composer",
 *     zh-CN:description="QueryPHP 系统服务提供者、应用命令、扩展配置和扩展语言包等都在 `composer` 中进行定义。",
 * )
 */
final class ComposerOptionTest extends TestCase
{
    /**
     * @api(
     *     zh-CN:title="composer.json ",
     *     zh-CN:description="
     * 示例配置
     *
     * ``` json
     * {[file_get_contents('vendor/hunzhiwange/framework/tests/Option/app1/composer.json')]}
     * ```
     *
     * 相关配置约定在 `leevel` 字段中，可以非常方便地扩展系统。
     * ",
     *     zh-CN:note="",
     * )
     */
    public function testBaseUse(): void
    {
        $options = ($composerOption = new ComposerOption(__DIR__.'/app1'))->loadData();

        $data = <<<'eot'
            {
                "providers": [
                    "Tests\\Option\\Providers\\Foo",
                    "Tests\\Option\\Providers\\Bar",
                    "Demo\\Provider\\Register",
                    "Common\\Infra\\Provider\\Event",
                    "Common\\Infra\\Provider\\Router"
                ],
                "ignores": [
                    "Leevel\\Notexits\\Provider\\Register"
                ],
                "commands": [
                    "Tests\\Option\\Commands\\Test",
                    "Tests\\Option\\Commands\\Console",
                    "Demo\\Demo\\Console",
                    "Common\\App\\Console"
                ],
                "options": {
                    "demo": "option\/extend\/test.php"
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
                $options
            )
        );

        static::assertSame(
            $data,
            $this->varJson(
                $composerOption->loadData()
            )
        );
    }

    public function testComposerNotFound(): void
    {
        $options = ($composerOption = new ComposerOption(__DIR__.'/app4'))->loadData();

        $data = <<<'eot'
            {
                "providers": [
                    "Tests\\Option\\Providers\\Foo",
                    "Tests\\Option\\Providers\\Bar",
                    "Demo\\Provider\\Register"
                ],
                "ignores": [],
                "commands": [
                    "Tests\\Option\\Commands\\Test",
                    "Tests\\Option\\Commands\\Console",
                    "Demo\\Demo\\Console"
                ],
                "options": [],
                "i18ns": [],
                "i18n-paths": [],
                "metas": []
            }
            eot;

        static::assertSame(
            $data,
            $this->varJson(
                $options
            )
        );
    }
}
