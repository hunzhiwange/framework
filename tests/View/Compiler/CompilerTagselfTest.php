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

namespace Tests\View\Compiler;

use Tests\TestCase;

/**
 * compiler tagself test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.06.07
 *
 * @version 1.0
 *
 * @api(
 *     title="Tagself 保护标签",
 *     path="template/tagself",
 *     description="可以使用 tagself 标签来防止模板标签被解析，在特殊场景非常有用。",
 * )
 */
class CompilerTagselfTest extends TestCase
{
    use Compiler;

    /**
     * @api(
     *     title="基本使用",
     *     description="",
     *     note="上面的 **if 标签** 被 **tagself** 标签包含，因此 **if 标签** 里面的内容并不会被模板引擎解析，而是保持原样输出。",
     * )
     */
    public function testBaseUse(): void
    {
        $parser = $this->createParser();

        $source = <<<'eot'
            <tagself>
               <if condition="$name eq 1 "> value1
                  <elseif condition="$name eq 2" />value2
                  <else /> value3
               </if>
            </tagself>
            
            {tagself}
                 {{i + 1}}
                 {$value}
            {/tagself}
            eot;

        $compiled = <<<'eot'
            <if condition="$name eq 1 "> value1
                  <elseif condition="$name eq 2" />value2
                  <else /> value3
               </if>
            
            {{i + 1}}
                 {$value}
            eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));
    }
}
