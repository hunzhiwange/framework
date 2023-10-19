<?php

declare(strict_types=1);

namespace Tests\View\Compiler;

use Leevel\Kernel\Utils\Api;
use Tests\TestCase;

#[Api([
    'zh-CN:title' => 'Tagself 保护标签',
    'path' => 'template/tagself',
    'zh-CN:description' => <<<'EOT'
可以使用 tagself 标签来防止模板标签被解析，在特殊场景非常有用。
EOT,
])]
final class CompilerTagselfTest extends TestCase
{
    use Compiler;

    #[Api([
        'zh-CN:title' => '基本使用',
        'zh-CN:note' => <<<'EOT'
上面的 **if 标签** 被 **tagself** 标签包含，因此 **if 标签** 里面的内容并不会被模板引擎解析，而是保持原样输出。
EOT,
    ])]
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

        static::assertSame($compiled, $parser->doCompile($source, null, true));
    }
}
