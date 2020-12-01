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
 * (c) 2010-2020 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\View\Compiler;

use Tests\TestCase;

/**
 * @api(
 *     zh-CN:title="Tagself 保护标签",
 *     path="template/tagself",
 *     zh-CN:description="可以使用 tagself 标签来防止模板标签被解析，在特殊场景非常有用。",
 * )
 */
class CompilerTagselfTest extends TestCase
{
    use Compiler;

    /**
     * @api(
     *     zh-CN:title="基本使用",
     *     zh-CN:description="",
     *     zh-CN:note="上面的 **if 标签** 被 **tagself** 标签包含，因此 **if 标签** 里面的内容并不会被模板引擎解析，而是保持原样输出。",
     * )
     */
    public function testBaseUse(): void
    {
        $parser = $this->createParser();

        $source = <<<'eot'
            {% tagself %}
                {% if cond="1 == $name" %} value1
                {% elseif cond="2 == $name" %} value2
                {% else %} value3
                {% :if %}
            {% :tagself %}
            
            {% tagself %}
                 {{ $i + 1 }}
                 {{ $value }}
            {% :tagself %}
            eot;

        $compiled = <<<'eot'
            {% if cond="1 == $name" %} value1
                {% elseif cond="2 == $name" %} value2
                {% else %} value3
                {% :if %}
            
            {{ $i + 1 }}
                 {{ $value }}
            eot;

        $this->assertSame($compiled, $parser->doCompile($source, null, true));
    }
}
