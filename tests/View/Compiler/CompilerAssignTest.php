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

namespace Tests\View\Compiler;

use Tests\TestCase;

/**
 * compiler assign test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.06.07
 *
 * @version 1.0
 * @coversNothing
 */
class CompilerAssignTest extends TestCase
{
    use Compiler;

    public function testBaseUse()
    {
        $parser = $this->createParser();

        $source = <<<'eot'
<assign name="helloWorld" value="say hello to the world" />
<assign name="test.hello" value="hello" />
eot;

        $compiled = <<<'eot'
<?php $helloWorld='say hello to the world';?>
<?php $test->hello='hello';?>
eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));

        $source = <<<'eot'
{% let foo = 'foo' %}
{% let hello = hello . 'foo' %}
eot;

        $compiled = <<<'eot'
<?php $foo = 'foo';?>
<?php $hello = $hello . 'foo';?>
eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));
    }
}
