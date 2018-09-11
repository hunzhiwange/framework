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
 */
class I18nTest extends TestCase
{
    public function testBaseUse()
    {
        $i18n = new I18n('zh-CN');

        $this->assertSame('zh-CN', $i18n->getI18n());
        $this->assertSame([
            'zh-CN' => [],
        ], $i18n->all());
        $this->assertSame('中国语言', $i18n->gettext('中国语言'));
        $this->assertSame('中国人语言', $i18n->__('中国%s语言', '人'));
    }

    public function testGetText()
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

    public function testAll()
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

    public function testSetI18n()
    {
        $i18n = new I18n('zh-CN');

        $this->assertSame('zh-CN', $i18n->getI18n());

        $i18n->setI18n('en-US');
        $this->assertSame('en-US', $i18n->getI18n());
    }
}
