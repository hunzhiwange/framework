<?php

declare(strict_types=1);

namespace Leevel\View;

/**
 * 编译器列表.
 */
class Compiler
{
    /**
     * code 支持的特殊别名映射.
     */
    protected array $codeMap = [
        'php' => '~',
        'note' => '#',
        'variable' => '$',
        'echo' => ':',
    ];

    /**
     * node 支持的特殊别名映射.
     */
    protected array $nodeMap = [
        'foreachPlus' => 'foreach+',
    ];

    /**
     * Node 标签.
     */
    protected array $nodeTag = [
        'if' => [
            'attr' => [
                'cond',
            ],
            'single' => false,
            'required' => [
                'cond',
            ],
        ],
        'elseif' => [
            'attr' => [
                'cond',
            ],
            'single' => true,
            'required' => [
                'cond',
            ],
        ],
        'else' => [
            'attr' => [],
            'single' => true,
            'required' => [],
        ],
        'foreachPlus' => [
            'attr' => [
                'index',
                'key',
                'mod',
                'empty',
                'length',
                'offset',
                'name',
                'id',
            ],
            'single' => false,
            'required' => [
                'name',
            ],
        ],
        'foreach' => [
            'attr' => [
                'for',
                'key',
                'value',
                'index',
            ],
            'single' => false,
            'required' => [
                'for',
            ],
        ],
        'include' => [
            'attr' => [
                'file',
                'ext',
            ],
            'single' => true,
            'required' => [
                'file',
            ],
        ],
        'for' => [
            'attr' => [
                'step',
                'start',
                'end',
                'var',
                'type',
            ],
            'single' => false,
            'required' => [],
        ],
        'while' => [
            'attr' => [
                'cond',
            ],
            'single' => false,
            'required' => [
                'cond',
            ],
        ],
        'break' => [
            'attr' => [],
            'single' => true,
            'required' => [],
        ],
        'continue' => [
            'attr' => [],
            'single' => true,
            'required' => [],
        ],
    ];

    /**
     * 获取编译器.
     *
     * @throws \Exception
     */
    public function getCompilers(): array
    {
        $methods = get_class_methods($this);
        $compilers = [];
        foreach ($methods as $method) {
            if ('Compiler' !== substr($method, -8)) {
                continue;
            }

            $method = substr($method, 0, -8);
            if (!\in_array($method, ['global', 'globalrevert', 'revert'], true)) {
                $type = strtolower(substr($method, -4));
                $tag = substr($method, 0, -4);
                if ('code' === $type) {
                    $name = $this->codeMap[$tag] ?? $tag;
                } elseif ('node' === $type) {
                    $name = $this->nodeMap[$tag] ?? $tag;
                } else {
                    throw new \Exception('Compiler was not found.'); // @codeCoverageIgnore
                }
                $compilers[] = [$type, $name, $tag];
            }
        }

        return $compilers;
    }

    /**
     * node.tag.
     */
    public function getNodeTagHelp(): array
    {
        return $this->nodeTag;
    }

    /**
     * 全局编译器.
     */
    public function globalCompiler(array &$theme): void
    {
        $theme['content'] = $this->encodeContent($theme['content'], 'global');
    }

    /**
     * 全局还原编译器.
     */
    public function globalrevertCompiler(array &$theme): void
    {
        $theme['content'] = $this->encodeContent($theme['content'], 'revert');
    }

    /**
     * node.code 还原编译器.
     */
    public function revertCompiler(array &$theme): void
    {
        $theme['content'] = $this->encodeContent($theme['content'], 'revert');
    }

    /**
     * 变量编译器.
     */
    public function variableCodeCompiler(array &$theme): void
    {
        $theme['content'] = !empty($theme['content']) ?
            $this->parseContent($theme['content']) : null;

        if (null !== $theme['content']) {
            $theme['content'] = $this->withPhpTag('echo '.$theme['content'].';');
        }

        $theme['content'] = $this->encodeContent($theme['content']);
    }

    /**
     * php 脚本编译器.
     */
    public function phpCodeCompiler(array &$theme): void
    {
        $theme['content'] = $this->encodeContent(
            $this->withPhpTag($theme['content'].';')
        );
    }

    /**
     * 注释编译器.
     */
    public function noteCodeCompiler(array &$theme): void
    {
        $theme['content'] = $this->encodeContent(' ');
    }

    /**
     * PHP echo 标签编译器.
     */
    public function echoCodeCompiler(array &$theme): void
    {
        $theme['content'] = $this->encodeContent(
            $this->withPhpTag('echo '.$theme['content'].';')
        );
    }

    /**
     * if 编译器.
     */
    public function ifNodeCompiler(array &$theme): void
    {
        $this->checkNode($theme);
        $attr = $this->getNodeAttribute($theme);
        $theme['content'] = $this->withPhpTag('if ('.$attr['cond'].'):').
            $this->getNodeBody($theme).
            $this->withPhpTag('endif;');
    }

    /**
     * elseif 编译器.
     */
    public function elseifNodeCompiler(array &$theme): void
    {
        $this->checkNode($theme);
        $attr = $this->getNodeAttribute($theme);
        $theme['content'] = $this->withPhpTag('elseif ('.$attr['cond'].'):');
    }

    /**
     * else 编译器.
     */
    public function elseNodeCompiler(array &$theme): void
    {
        $this->checkNode($theme);
        $theme['content'] = $this->withPhpTag('else:');
    }

    /**
     * foreach 增强版编译器.
     */
    public function foreachPlusNodeCompiler(array &$theme): void
    {
        $this->checkNode($theme);
        $attr = $this->getNodeAttribute($theme);
        null === $attr['index'] && $attr['index'] = 'index';
        null === $attr['key'] && $attr['key'] = 'key';
        null === $attr['id'] && $attr['id'] = 'id';
        null === $attr['mod'] && $attr['mod'] = 2;

        if (preg_match('/[^\\d\-.,]/', (string) $attr['mod'])) {
            $attr['mod'] = '$'.$attr['mod'];
        }

        null === $attr['empty'] && $attr['empty'] = '';
        null === $attr['length'] && $attr['length'] = '';
        null === $attr['offset'] && $attr['offset'] = '';
        $attr['name'] = $this->parseContent($attr['name']);

        $compiled = [];
        $tmp = 'if (is_array('.
            $attr['name'].')):'.PHP_EOL.'    $'.
            $attr['index'].' = 0;'.PHP_EOL;

        if ('' !== $attr['length']) {
            $tmp .= '    $tmp = array_slice('.
                $attr['name'].', '.
                $attr['offset'].', '.
                $attr['length'].');';
        } elseif ('' !== $attr['offset']) {
            $tmp .= '    $tmp = array_slice('.
                $attr['name'].', '.
                $attr['offset'].');';
        } else {
            $tmp .= '    $tmp = '.$attr['name'].';';
        }

        $tmp .= PHP_EOL.'    if (0 === count($tmp)):'.PHP_EOL.
            '        echo "'.$attr['empty'].'";';
        $tmp .= PHP_EOL.'    else:';
        $tmp .= PHP_EOL.'        foreach ($tmp as $'.$attr['key'].
            ' => $'.$attr['id'].'):';
        $tmp .= PHP_EOL.'            ++$'.$attr['index'].';'.PHP_EOL;
        $tmp .= '            '.'$mod = $'.$attr['index'].' % '.
            $attr['mod'].';';

        $compiled[] = $this->withPhpTag($tmp);
        $compiled[] = $this->getNodeBody($theme);
        $compiled[] = '        '.$this->withPhpTag(
            'endforeach;'.PHP_EOL.'    endif;'.
            PHP_EOL.'else:'.PHP_EOL.'    echo "'.
            $attr['empty'].'";'.PHP_EOL.'endif;'
        );
        $theme['content'] = implode('', $compiled);
    }

    /**
     * foreach 编译器.
     */
    public function foreachNodeCompiler(array &$theme): void
    {
        $this->checkNode($theme);
        $attr = $this->getNodeAttribute($theme);

        foreach (['key', 'value', 'index'] as $key) {
            null === $attr[$key] && $attr[$key] = '$'.$key;
        }

        foreach (['for', 'key', 'value', 'index'] as $key) {
            if ('$'.$key === $attr[$key]) {
                continue;
            }
            $attr[$key] = $this->parseContent($attr[$key]);
        }

        // 编译
        $theme['content'] = $this->withPhpTag($attr['index'].' = 1;').PHP_EOL.
            $this->withPhpTag(
                'if (is_array('.$attr['for'].')): foreach ('.$attr['for'].
                ' as '.$attr['key'].' => '.
                $attr['value'].'):'
            ).
            $this->getNodeBody($theme).
            $this->withPhpTag($attr['index'].'++;').PHP_EOL.
            $this->withPhpTag('endforeach; endif;');
    }

    /**
     * include 编译器.
     */
    public function includeNodeCompiler(array &$theme): void
    {
        $this->checkNode($theme);
        $attr = $this->getNodeAttribute($theme);

        if (preg_match('/^\((.+)\)$/', $attr['file'], $matches)) {
            $attr['file'] = $matches[1];
        } else {
            // 后缀由主模板提供
            if (!$attr['ext'] && str_contains($attr['file'], '.')) {
                $temp = explode('.', $attr['file']);
                $attr['ext'] = '.'.array_pop($temp);
                $attr['file'] = implode('.', $temp);
            }

            if (!str_starts_with($attr['file'], '$')) {
                $attr['file'] = '\''.$attr['file'].'\'';
            }
        }

        $theme['content'] = $this->withPhpTag(
            'echo $this->display('.$attr['file'].
            ($attr['ext'] ? ", [], '{$attr['ext']}'" : '').');'
        );
    }

    /**
     * for 编译器.
     */
    public function forNodeCompiler(array &$theme): void
    {
        $this->checkNode($theme);
        $attr = $this->getNodeAttribute($theme);
        null === $attr['step'] && $attr['step'] = '1';
        null === $attr['start'] && $attr['start'] = '0';
        null === $attr['end'] && $attr['end'] = '0';
        null === $attr['var'] && $attr['var'] = 'var';
        $attr['var'] = '$'.$attr['var'];

        if ('-' === $attr['type']) {
            $comparison = ' >= ';
            $minusPlus = ' -= ';
        } else {
            $comparison = ' <= ';
            $minusPlus = ' += ';
        }

        $compiled = [];
        $compiled[] = $this->withPhpTag(
            'for ('.$attr['var'].
            ' = '.$attr['start'].'; '.
            $attr['var'].$comparison.$attr['end'].'; '.
            $attr['var'].$minusPlus.$attr['step'].'):'
        );
        $compiled[] = $this->getNodeBody($theme);
        $compiled[] = $this->withPhpTag('endfor;');
        $theme['content'] = implode('', $compiled);
    }

    /**
     * while 编译器.
     */
    public function whileNodeCompiler(array &$theme): void
    {
        $this->checkNode($theme);
        $attr = $this->getNodeAttribute($theme);
        $theme['content'] = $this->withPhpTag('while('.$attr['cond'].'):').
            $this->getNodeBody($theme).
            $this->withPhpTag('endwhile;');
    }

    /**
     * break 编译器.
     */
    public function breakNodeCompiler(array &$theme): void
    {
        $this->checkNode($theme);
        $theme['content'] = $this->withPhpTag('break;');
    }

    /**
     * continue 编译器.
     */
    public function continueNodeCompiler(array &$theme): void
    {
        $this->checkNode($theme);
        $theme['content'] = $this->withPhpTag('continue;');
    }

    /**
     * 属性编译.
     */
    public function attributeNodeCompiler(array &$theme): void
    {
        $source = trim($theme['content']);
        $source = $this->escapeCharacter($source);
        $tag = $this->nodeTag;
        $allowedAttr = $tag[$theme['parent_name']]['attr'];

        // 正则匹配
        $regexp = [];
        // xxx="yyy" 或 "yyy" 格式
        $regexp[] = '/(([^=\\s]+)=)?"([^"]+)"/';
        // xxx='yyy' 或 'yyy' 格式
        $regexp[] = "/(([^=\\s]+)=)?'([^\\']+)'/";
        // xxx=yyy 或 yyy 格式
        $regexp[] = '/(([^=\\s]+)=)?([^\\s]+)/';
        $nameIdx = 2;
        $valueIdx = 3;
        $defaultIdx = 0;
        foreach ($regexp as $item) {
            if (preg_match_all($item, $source, $res)) {
                foreach ($res[0] as $idx => $attribute) {
                    $source = str_replace($attribute, '', $source);
                    if (empty($res[$nameIdx][$idx])) {
                        $name = 'cond'.($defaultIdx ?: '');
                        ++$defaultIdx;
                    } else {
                        $name = $res[$nameIdx][$idx];
                    }

                    $value = $res[$valueIdx][$idx];
                    $value = $this->escapeCharacter($value, false);
                    $theme['attribute_list'][strtolower($name)] = $value;
                }
            }
        }

        // 补全节点其余参数
        foreach ($allowedAttr as $item) {
            if (!isset($theme['attribute_list'][$item])) {
                $theme['attribute_list'][$item] = null;
            }
        }

        $theme['content'] = $source;
    }

    /**
     * 解析变量内容.
     */
    protected function parseContent(string $content): string
    {
        // 以 | 分割字符串，数组第一位是变量名字符串，之后的都是函数参数
        // exp: {{ $hello|md5 }}
        $contents = explode('|', $content);

        // 弹出第一个元素,也就是变量名
        $name = (string) array_shift($contents);
        if (!str_starts_with($name, '$')) {
            $name = '$'.$name;
        }

        // 如果有使用函数
        if (\count($contents) > 0) {
            $name = $this->parseVarFunction($name, $contents);
        }

        return $name;
    }

    /**
     * 解析函数.
     */
    protected function parseVarFunction(string $name, array $var): string
    {
        $len = \count($var);
        for ($index = 0; $index < $len; ++$index) {
            if (0 === stripos($var[$index], 'default=')) {
                $args = explode('=', $var[$index], 2);
            } else {
                $args = explode('=', $var[$index]);
            }

            $args[0] = trim($args[0]);
            if (isset($args[1])) {
                $args[1] = str_replace('->', ':', $args[1]);
            }

            switch (strtolower($args[0])) {
                // 特殊模板函数
                case 'default':
                    $name = $name.' ?: '.$args[1];

                    break;
                    // 通用模板函数
                default:
                    if (isset($args[1])) {
                        if (strstr($args[1], '**')) {
                            $args[1] = str_replace('**', $name, $args[1]);
                            $name = "{$args[0]}({$args[1]})";
                        } else {
                            $name = "{$args[0]}({$name}, {$args[1]})";
                        }
                    } elseif (!empty($args[0])) {
                        $name = "{$args[0]}({$name})";
                    }
            }
        }

        return $name;
    }

    /**
     * 编码内容.
     */
    protected function encodeContent(string $content, string $type = ''): string
    {
        if ('global' === $type) {
            $content = Parser::globalEncode($content);
        } elseif (\in_array($type, ['revert', 'include'], true)) {
            $content = base64_decode($content, true) ?: '';
        } else {
            $content = Parser::revertEncode($content);
        }

        return $content;
    }

    /**
     * 验证节点是否正确.
     *
     * @throws \InvalidArgumentException
     */
    protected function checkNode(array $theme): bool
    {
        $attribute = $theme['children'][0];

        // 验证标签的属性值
        if (true !== $attribute['is_attribute']) {
            throw new \InvalidArgumentException('Tag attribute type validation failed.');
        }

        // 验证必要属性
        $tag = $this->nodeTag;
        if (!isset($tag[$theme['name']])) {
            throw new \InvalidArgumentException(sprintf('The tag %s is undefined.', $theme['name']));
        }

        foreach ($tag[$theme['name']]['required'] as $name) {
            $name = strtolower($name);
            if (!isset($attribute['attribute_list'][$name])) {
                throw new \InvalidArgumentException(sprintf('The node %s lacks the required property: %s.', $theme['name'], $name));
            }
        }

        return true;
    }

    /**
     * 取得节点的属性列表.
     */
    protected function getNodeAttribute(array $theme): array
    {
        foreach ($theme['children'] ?? [] as $child) {
            if (isset($child['is_attribute']) && true === $child['is_attribute']) {
                return $child['attribute_list'];
            }
        }

        return [];
    }

    /**
     * 取得body编译内容.
     */
    protected function getNodeBody(array $theme): string
    {
        foreach ($theme['children'] ?? [] as $child) {
            if (isset($child['is_body']) && true === $child['is_body']) {
                return $child['content'];
            }
        }

        return '';
    }

    /**
     * 正则属性转义.
     */
    protected function escapeCharacter(string $txt, bool $esc = true): string
    {
        if ('""' === $txt) {
            $txt = '';
        }

        if ($esc) {
            // 转义
            $txt = str_replace([
                '\\\\',
                "\\'",
                '\\"',
                '\\$',
                '\\.',
            ], [
                '\\',
                '~~{#!`!#}~~',
                '~~{#!``!#}~~',
                '~~{#!S!#}~~',
                '~~{#!dot!#}~~',
            ], $txt);
        } else {
            // 还原
            $txt = str_replace([
                '~~{#!`!#}~~',
                '~~{#!``!#}~~',
                '~~{#!S!#}~~',
                '~~{#!dot!#}~~',
            ], [
                "'",
                '"',
                '$',
                '.',
            ], $txt);
        }

        return $txt;
    }

    /**
     * PHP 标签包裹内容.
     */
    protected function withPhpTag(string $content): string
    {
        return $this->phpTagStart().$content.$this->phpTagEnd();
    }

    /**
     * PHP 开始标签.
     */
    protected function phpTagStart(): string
    {
        return '<?php ';
    }

    /**
     * PHP 结束标签.
     */
    protected function phpTagEnd(): string
    {
        return ' ?>';
    }
}
