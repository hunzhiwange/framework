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

namespace Tests\I18n;

use Leevel\I18n\I18n;
use Tests\TestCase;

/**
 * i18n test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.06.03
 *
 * @version 1.0
 *
 * @api(
 *     title="国际化多语言",
 *     path="component/i18n",
 *     description="
 * QueryPHP 内置通过 `i18n` 语言包提供多语言支持，可以在开发过程中预先做好语言功能，为产品国际化做好准备。
 *
 * ### 基本使用
 *
 * 使用助手函数
 *
 * ``` php
 * \Leevel\I18n\Helper::gettext(string $text, ...$data): string;
 * ```
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
 *
 * ### 相关文件
 *
 *  * 国际化语言配置位于 `option\i18n.php`，可以定义当前的语言。
 *  * 主要语言包文件位于 `i18n` 目录，包含 `zh-CN`、`zh-TW`、`en-US`。
 *  * 扩展语言包 `common/ui/i18n` 目录，在 `composer.json` 中定义。
 *
 * QueryPHP 会自动扫面语言包文件完成翻译，无需人工干预。
 * ",
 * )
 */
class I18nTest extends TestCase
{
    /**
     * @api(
     *     title="多语言基本使用",
     *     description="",
     *     note="",
     * )
     */
    public function testBaseUse(): void
    {
        $i18n = new I18n('zh-CN');

        $this->assertSame('zh-CN', $i18n->getI18n());
        $this->assertSame([
            'zh-CN' => [],
        ], $i18n->all());
        $this->assertSame('中国语言', $i18n->gettext('中国语言'));
        $this->assertSame('中国人语言', $i18n->__('中国%s语言', '人'));
    }

    /**
     * @api(
     *     title="多语言翻译测试",
     *     description="",
     *     note="",
     * )
     */
    public function testGetText(): void
    {
        $i18n = new I18n('en-US');

        $this->assertSame('世界你好', $i18n->gettext('世界你好'));
        $this->assertSame('胡巴 ye', $i18n->__('胡巴 %s', 'ye'));

        $i18n->addtext('en-US', [
            '世界你好'  => 'hello world',
            '胡巴 %s' => 'foo %s',
        ]);

        $this->assertSame('hello world', $i18n->gettext('世界你好'));
        $this->assertSame('foo ye', $i18n->__('胡巴 %s', 'ye'));
    }

    /**
     * @api(
     *     title="all 获取所有语言项",
     *     description="",
     *     note="",
     * )
     */
    public function testAll(): void
    {
        $i18n = new I18n('zh-CN');

        $i18n->addtext('en-US', [
            '世界你好'  => 'hello world',
            '胡巴 %s' => 'foo %s',
        ]);

        $this->assertSame([
            'zh-CN' => [],
            'en-US' => [
                '世界你好'  => 'hello world',
                '胡巴 %s' => 'foo %s',
            ],
        ], $i18n->all());
    }

    /**
     * @api(
     *     title="切换语言",
     *     description="",
     *     note="",
     * )
     */
    public function testSetI18n(): void
    {
        $i18n = new I18n('zh-CN');

        $this->assertSame('zh-CN', $i18n->getI18n());

        $i18n->setI18n('en-US');
        $this->assertSame('en-US', $i18n->getI18n());
    }
}
