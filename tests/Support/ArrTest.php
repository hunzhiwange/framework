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

namespace Tests\Support;

use Leevel\Support\Arr;
use Tests\TestCase;

/**
 * arr test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.07.15
 *
 * @version 1.0
 */
class ArrTest extends TestCase
{
    public function testBaseUse()
    {
        $this->assertTrue(Arr::normalize(true));

        $this->assertSame(['a', 'b'], Arr::normalize('a,b'));

        $this->assertSame(['a', 'b'], Arr::normalize(['a', 'b']));

        $this->assertSame(['a'], Arr::normalize(['a', '']));

        $this->assertSame(['a'], Arr::normalize(['a', ''], ',', true));

        $this->assertSame(['a', ' 0 '], Arr::normalize(['a', ' 0 '], ',', true));

        $this->assertSame(['a', '0'], Arr::normalize(['a', ' 0 '], ','));
    }
}
