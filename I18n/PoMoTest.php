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

use Leevel\I18n\Mo;
use Leevel\I18n\Po;
use Tests\TestCase;

/**
 * po mo test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.06.03
 *
 * @version 1.0
 * @coversNothing
 */
class PoMoTest extends TestCase
{
    public function testPo()
    {
        $lang = (new Po())->readToArray([__DIR__.'/page.po']);

        $this->assertSame($lang, [
            '上一页'    => 'Previous',
            '下一页'    => 'Next',
            '共 %d 条' => 'Total %d',
            '前往'     => 'Go to',
            '页'      => 'Page',
        ]);
    }

    public function testMo()
    {
        $lang = (new Mo())->readToArray([__DIR__.'/page.mo']);

        $this->assertSame($lang, [
            '上一页'    => 'Previous',
            '下一页'    => 'Next',
            '共 %d 条' => 'Total %d',
            '前往'     => 'Go to',
            '页'      => 'Page',
        ]);
    }
}
