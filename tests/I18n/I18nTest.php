<?php

declare(strict_types=1);

namespace Tests\I18n;

use Leevel\I18n\I18n;
use Tests\TestCase;

/**
 * @api(
 *     zh-CN:title="国际化多语言",
 *     path="component/i18n",
 *     zh-CN:description="
 * QueryPHP 内置通过 `i18n` 语言包提供多语言支持，可以在开发过程中预先做好语言功能，为产品国际化做好准备。
 *
 * ### 基本使用
 *
 * 使用容器 i18n 服务
 *
 * ``` php
 * \App::make('i18n')->gettext(string $text, ...$data): string;
 * \App::make('i18n')->__(string $text, ...$data): string;
 * ```
 *
 * 推荐使用全局函数 `__`
 *
 * ``` php
 * __(string $text, ...$data): string;
 * ```
 *
 * ### 语言目录
 *
 *  * 国际化语言配置位于 `option/i18n.php`，可以定义当前的语言。
 *  * 主要语言包文件位于 `i18n` 目录，包含 `zh-CN`、`zh-TW`、`en-US`。
 *  * 扩展语言包 `common/ui/i18n` 目录，在 `composer.json` 中定义。
 *
 * QueryPHP 会自动扫面语言包文件完成翻译，无需人工干预。
 *
 * composer.json 可以扩展目录
 *
 * ``` json
 * {
 *     "extra": {
 *         "leevel": {
 *             "@i18ns": "The extend i18ns",
 *             "i18ns": {
 *                 "test": "common/ui/i18n"
 *             }
 *         }
 *     }
 * }
 *
 * 注意，其它软件包也可以采用这种方式自动注入扩展默认语言。
 *
 * ### 语言缓存
 *
 * 可以结合 `poedit` 软件扫描为 `po` 文件，系统会自动解析为数组。
 *
 * 例外语言包支持生成缓存，通过内置的命令即可实现。
 *
 * ``` sh
 * php leevel i18n:cache
 * ```
 *
 * 返回结果
 *
 * ```
 * I18n cache file /data/codes/queryphp/bootstrap/i18n/zh-CN.php cache successed.
 * I18n cache file /data/codes/queryphp/bootstrap/i18n/zh-TW.php cache successed.
 * I18n cache file /data/codes/queryphp/bootstrap/i18n/en-US.php cache successed.
 * I18n cache files cache successed.
 * ```
 *
 * ``` sh
 * php leevel i18n:clear
 * ```
 *
 * 返回结果
 *
 * ```
 * Start to clear i18n.
 * I18n cache file /data/codes/queryphp/bootstrap/i18n/zh-CN.php clear successed.
 * I18n cache file /data/codes/queryphp/bootstrap/i18n/zh-TW.php clear successed.
 * I18n cache file /data/codes/queryphp/bootstrap/i18n/en-US.php clear successed.
 * I18n cache files clear successed.
 * ```
 * ",
 * )
 */
final class I18nTest extends TestCase
{
    /**
     * @api(
     *     zh-CN:title="多语言基本使用",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testBaseUse(): void
    {
        $i18n = new I18n('zh-CN');

        static::assertSame('zh-CN', $i18n->getI18n());
        static::assertSame([
            'zh-CN' => [],
        ], $i18n->all());
        static::assertSame('中国语言', $i18n->gettext('中国语言'));
        static::assertSame('中国人语言', $i18n->gettext('中国%s语言', '人'));
    }

    /**
     * @api(
     *     zh-CN:title="多语言翻译测试",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testGetText(): void
    {
        $i18n = new I18n('en-US');

        static::assertSame('世界你好', $i18n->gettext('世界你好'));
        static::assertSame('胡巴 ye', $i18n->gettext('胡巴 %s', 'ye'));

        $i18n->addtext('en-US', [
            '世界你好' => 'hello world',
            '胡巴 %s' => 'foo %s',
        ]);

        static::assertSame('hello world', $i18n->gettext('世界你好'));
        static::assertSame('foo ye', $i18n->gettext('胡巴 %s', 'ye'));
    }

    /**
     * @api(
     *     zh-CN:title="all 获取所有语言项",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testAll(): void
    {
        $i18n = new I18n('zh-CN');

        $i18n->addtext('en-US', [
            '世界你好' => 'hello world',
            '胡巴 %s' => 'foo %s',
        ]);

        static::assertSame([
            'zh-CN' => [],
            'en-US' => [
                '世界你好' => 'hello world',
                '胡巴 %s' => 'foo %s',
            ],
        ], $i18n->all());
    }

    /**
     * @api(
     *     zh-CN:title="切换语言",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testSetI18n(): void
    {
        $i18n = new I18n('zh-CN');

        static::assertSame('zh-CN', $i18n->getI18n());

        $i18n->setI18n('en-US');
        static::assertSame('en-US', $i18n->getI18n());
    }
}
