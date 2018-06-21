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

namespace Tests\Tree;

use Leevel\Tree\Tree;
use Tests\TestCase;

/**
 * tree test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.06.21
 *
 * @version 1.0
 * @coversNothing
 */
class TreeTest extends TestCase
{
    public function testBaseUse()
    {
        $tree = new Tree([
            [1, 0, 'hello'],
            [2, 1, 'world'],
        ]);

$json = <<<'eot'
[{"value":1,"data":"hello","children":[{"value":2,"data":"world"}]}]
eot;

        $this->assertSame($json, $tree->toJson());
    }
}
