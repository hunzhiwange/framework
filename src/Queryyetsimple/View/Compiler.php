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

namespace Leevel\View;

use InvalidArgumentException;

/**
 * 编译器列表.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2016.11.18
 *
 * @version 1.0
 */
class Compiler implements ICompiler
{
    /**
     * code 支持的特殊别名映射.
     *
     * @var array
     */
    protected $codeMap = [
        'php'      => '~',
        'note'     => '#',
        'variable' => '$',
        'foreach'  => 'list', // foreach 和 for 冲突，foreach 改为 list
        'echo'     => ':',
        'endtag'   => [
            '/list',
            '/for',
            '/while',
            '/if',
            '/script',
            '/style',
        ],
    ];

    /**
     * node 支持的特殊别名映射.
     *
     * @var array
     */
    protected $nodeMap = [];

    /**
     * js 风格支持的特殊别名映射.
     *
     * @var array
     */
    protected $jsMap = [];

    /**
     * js 风格标签.
     *
     * @var array
     */
    protected $jsTag = [
        // required 属性不能为空，single 单标签
        'if' => [
            'attr' => [
                'condition1',
            ],
            'single'   => false,
            'required' => [
                'condition1',
            ],
        ],
        'elseif' => [
            'attr' => [
                'condition1',
            ],
            'single'   => true,
            'required' => [
                'condition1',
            ],
        ],
        'else' => [
            'attr'     => [],
            'single'   => true,
            'required' => [],
        ],
        'let' => [
            'attr' => [
                'condition1',
            ],
            'single'   => true,
            'required' => [
                'condition1',
            ],
        ],
        'for' => [
            'attr' => [
                'condition1',
                'condition2',
                'condition3',
            ],
            'single'   => false,
            'required' => [
                'condition1',
                'condition2',
                'condition3',
            ],
        ],
    ];

    /**
     * Node 标签.
     *
     * @var array
     */
    protected $nodeTag = [
        // required 属性不能为空，single 单标签
        'assign' => [
            'attr' => [
                'name',
                'value',
            ],
            'single'   => true,
            'required' => [
                'name',
            ],
        ],
        'if' => [
            'attr' => [
                'condition',
            ],
            'single'   => false,
            'required' => [
                'condition',
            ],
        ],
        'elseif' => [
            'attr' => [
                'condition',
            ],
            'single'   => true,
            'required' => [
                'condition',
            ],
        ],
        'else' => [
            'attr'     => [],
            'single'   => true,
            'required' => [],
        ],
        'list' => [
            'attr' => [
                'for',
                'key',
                'value',
                'index',
            ],
            'single'   => false,
            'required' => [
                'for',
            ],
        ],
        'lists' => [
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
            'single'   => false,
            'required' => [
                'name',
            ],
        ],
        'include' => [
            'attr' => [
                'file',
                'ext',
            ],
            'single'   => true,
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
            'single'   => false,
            'required' => [],
        ],
        'while' => [
            'attr' => [
                'condition',
            ],
            'single'   => false,
            'required' => [
                'condition',
            ],
        ],
        'break' => [
            'attr'     => [],
            'single'   => true,
            'required' => [],
        ],
        'continue' => [
            'attr'     => [],
            'single'   => true,
            'required' => [],
        ],
        'php' => [
            'attr'     => [],
            'single'   => false,
            'required' => [],
        ],
    ];

    /**
     * 构造函数.
     */
    public function __construct()
    {
    }

    /**
     * 获取编译器.
     *
     * @return array
     */
    public function getCompilers()
    {
        $methods = get_class_methods($this);
        $compilers = [];

        foreach ($methods as $method) {
            if ('Compiler' !== substr($method, -8)) {
                continue;
            }

            $method = substr($method, 0, -8);

            if (!in_array($method, [
                'global',
                'jsvar',
                'globalrevert',
                'revert',
            ], true)) {
                $type = strtolower(substr($method, -4));
                $tag = substr($method, 0, -4);

                if ('code' === $type) {
                    $name = $this->codeMap[$tag] ?? $tag;
                } elseif ('node' === $type) {
                    $name = $this->nodeMap[$tag] ?? $tag;
                } else {
                    $type = strtolower(substr($method, -2));
                    $tag = substr($method, 0, -2);
                    $name = $this->jsMap[$tag] ?? $tag;
                }

                $compilers[] = [
                    $type,
                    $name,
                    $tag,
                ];
            }
        }

        unset($methods);

        return $compilers;
    }

    /**
     * node.tag.
     *
     * @return array
     */
    public function getNodeTagHelp()
    {
        return $this->nodeTag;
    }

    /**
     * js.tag.
     *
     * @return array
     */
    public function getJsTagHelp()
    {
        return $this->jsTag;
    }

    /**
     * 全局编译器（保护标签）.
     *
     * @param array $theme
     */
    public function globalCompiler(array &$theme)
    {
        $theme['content'] = $this->encodeContent($theme['content'], 'global');
    }

    /**
     * 全局还原编译器（保护标签还原）.
     *
     * @param array $theme
     */
    public function globalrevertCompiler(array &$theme)
    {
        $theme['content'] = $this->encodeContent($theme['content'], 'revert');
    }

    /**
     * node.code 还原编译器.
     *
     * @param array $theme
     */
    public function revertCompiler(array &$theme)
    {
        $theme['content'] = $this->encodeContent($theme['content'], 'revert');
    }

    /**
     * 变量.
     *
     * @param array $theme
     */
    public function variableCodeCompiler(array &$theme)
    {
        $theme['content'] = !empty($theme['content']) ?
            $this->parseContent($theme['content']) : null;

        if (null !== $theme['content']) {
            $theme['content'] = $this->withPhpTag('echo '.$theme['content'].';');
        }

        $theme['content'] = $this->encodeContent($theme['content']);
    }

    /**
     * if.
     *
     * @param array $theme
     */
    public function ifCodeCompiler(array &$theme)
    {
        $theme['content'] = $this->parseContentIf($theme['content']);
        $theme['content'] = $this->encodeContent(
            $this->withPhpTag($theme['content'].':')
        );
    }

    /**
     * elseif.
     *
     * @param array $theme
     */
    public function elseifCodeCompiler(array &$theme)
    {
        $theme['content'] = $this->parseContentIf($theme['content'], 'else');
        $theme['content'] = $this->encodeContent(
            $this->withPhpTag($theme['content'].':')
        );
    }

    /**
     * else 标签.
     *
     * @param array $theme
     */
    public function elseCodeCompiler(array &$theme)
    {
        $theme['content'] = $this->encodeContent(
            $this->withPhpTag('else:')
        );
    }

    /**
     * foreach.
     *
     * @param array $theme
     */
    public function foreachCodeCompiler(array &$theme)
    {
        $theme['content'] = call_user_func(
            function ($content) {
                preg_match_all('/\\$([\S]+)/', $content, $matches);

                $matches = $matches[1];
                $num = count($matches);

                if ($num > 0) {
                    if (2 === $num) {
                        $result = "\${$matches[1]}";
                    } elseif (3 === $num) {
                        $result = "\${$matches[1]} => \${$matches[2]}";
                    } else {
                        throw new InvalidArgumentException('The parameter of code.foreach tag can be at most three.');
                    }

                    return "if (is_array(\${$matches[0]})): foreach(\${$matches[0]} as {$result})";
                }
            },
            $theme['content']
        );

        $theme['content'] = $this->encodeContent(
            $this->withPhpTag($theme['content'].':')
        );
    }

    /**
     * for.
     *
     * @param array $theme
     */
    public function forCodeCompiler(array &$theme)
    {
        $theme['content'] = $this->encodeContent(
            $this->withPhpTag('for ('.$theme['content'].'):')
        );
    }

    /**
     * while 头部.
     *
     * @param array $theme
     */
    public function whileCodeCompiler(array &$theme)
    {
        $theme['content'] = $this->encodeContent(
            $this->withPhpTag('while ('.$theme['content'].'):')
        );
    }

    /**
     * php 脚本.
     *
     * @param array $theme
     */
    public function phpCodeCompiler(array &$theme)
    {
        $theme['content'] = $this->encodeContent(
            $this->withPhpTag($theme['content'].';')
        );
    }

    /**
     * 注释.
     *
     * @param array $theme
     */
    public function noteCodeCompiler(array &$theme)
    {
        $theme['content'] = $this->encodeContent(' ');
    }

    /**
     * PHP echo 标签.
     *
     * @param array $theme
     */
    public function echoCodeCompiler(array &$theme)
    {
        $theme['content'] = $this->encodeContent(
            $this->withPhpTag('echo '.$theme['content'].';')
        );
    }

    /**
     * javascript 初始标签.
     *
     * @param array $theme
     */
    public function scriptCodeCompiler(array &$theme)
    {
        $theme['content'] = $this->encodeContent(
            '<script type="text/javascript">'
        );
    }

    /**
     * css 初始标签.
     *
     * @param array $theme
     */
    public function styleCodeCompiler(array &$theme)
    {
        $theme['content'] = $this->encodeContent(
            '<style type="text/css">'
        );
    }

    /**
     * endtag.
     *
     * @param array $theme
     */
    public function endtagCodeCompiler(array &$theme)
    {
        $theme['content'] = substr(
            $theme['source'],
            strpos($theme['source'], '/'),
            strripos($theme['source'], '}') - 1
        );

        $theme['content'] = call_user_func(
            function ($content) {
                $content = ltrim(trim($content), '/');

                switch ($content) {
                    case 'list':
                        $content = $this->withPhpTag('endforeach; endif;');

                        break;
                    case 'for':
                        $content = $this->withPhpTag('endfor;');

                        break;
                    case 'while':
                        $content = $this->withPhpTag('endwhile;');

                        break;
                    case 'if':
                        $content = $this->withPhpTag('endif;');

                        break;
                    case 'script':
                        $content = '</script>';

                        break;
                    case 'style':
                        $content = '</style>';

                        break;
                }

                return $content;
            }, $theme['content']
        );

        $theme['content'] = $this->encodeContent($theme['content']);
    }

    /**
     * 变量及表达式.
     *
     * @param array $theme
     */
    public function jsvarCompiler(array &$theme)
    {
        $theme['content'] = $this->withPhpTag(
            'echo '.$this->parseJcontent($theme['content']).';'
        );
    }

    /**
     * let 编译器.
     *
     * @param array $theme
     */
    public function letJsCompiler(array &$theme)
    {
        $this->checkNode($theme, true);
        $attr = $this->getNodeAttribute($theme);

        $name = array_shift($attr);
        $equal = array_shift($attr);

        if ('=' !== $equal) {
            array_unshift($attr, $equal);
        }

        if (!$attr) {
            $value = 'null';
        } else {
            $value = $this->parseExpression(implode(' ', $attr));

            if ('' === $value) {
                $value = 'null';
            }
        }

        $theme['content'] = $this->withPhpTag("\${$name} = ".$value.';');
    }

    /**
     * if 编译器.
     *
     * @param array $theme
     */
    public function ifJsCompiler(array &$theme)
    {
        $this->checkNode($theme, true);
        $attr = $this->getNodeAttribute($theme);

        $attr = $this->parseExpression(implode(' ', $attr));

        $theme['content'] = $this->withPhpTag("if ({$attr}):");
        $theme['content'] .= $this->getNodeBody($theme);
        $theme['content'] .= $this->withPhpTag('endif;');
    }

    /**
     * elseif 编译器.
     *
     * @param array $theme
     */
    public function elseifJsCompiler(array &$theme)
    {
        $this->checkNode($theme, true);
        $attr = $this->getNodeAttribute($theme);

        $attr = $this->parseExpression(implode(' ', $attr));

        $theme['content'] = $this->withPhpTag("elseif ({$attr}):");
        $theme['content'] .= $this->getNodeBody($theme);
    }

    /**
     * else.
     *
     * @param array $theme
     */
    public function elseJsCompiler(array &$theme)
    {
        $theme['content'] = $this->withPhpTag('else:');
    }

    /**
     * for 循环.
     *
     * @param array $theme
     */
    public function forJsCompiler(array &$theme)
    {
        $this->checkNode($theme, true);
        $attr = $this->getNodeAttribute($theme);
        $attr = array_values($attr);

        if (!in_array('in', $attr, true)) {
            throw new InvalidArgumentException('For tag need “in“ separate.');
        }

        $key = 'key';
        $value = array_shift($attr);

        if (false !== strpos($value, ',')) {
            list($key, $value) = explode(',', $value);
        }

        $next = array_shift($attr);

        if ('in' !== $next) {
            $key = $value;
            $value = $next;
            array_shift($attr);
        }

        $attr = $this->parseExpression(implode(' ', $attr));

        $theme['content'] = $this->withPhpTag("foreach ({$attr} as \${$key} => \${$value}):");
        $theme['content'] .= $this->getNodeBody($theme);
        $theme['content'] .= $this->withPhpTag('endforeach;');
    }

    /**
     * assign.
     *
     * @param array $theme
     */
    public function assignNodeCompiler(array &$theme)
    {
        $this->checkNode($theme);
        $attr = $this->getNodeAttribute($theme);

        $attr['name'] = $this->parseContent($attr['name'], false);

        if (null === $attr['value']) {
            $attr['value'] = 'null';
        } else {
            if ('$' === substr($attr['value'], 0, 1)) {
                $attr['value'] = $this->parseContent(substr($attr['value'], 1));
            } else {
                $attr['value'] = '\''.$attr['value'].'\'';
            }
        }

        // 编译
        $theme['content'] = $this->withPhpTag(
            $attr['name'].' = '.$attr['value'].';'
        );
    }

    /**
     * if.
     *
     * @param array $theme
     */
    public function ifNodeCompiler(array &$theme)
    {
        $this->checkNode($theme);
        $attr = $this->getNodeAttribute($theme);

        $attr['condition'] = $this->parseContentIf($attr['condition'], false);
        $theme['content'] = $this->withPhpTag('if ('.$attr['condition'].'):').
            $this->getNodeBody($theme).
            $this->withPhpTag('endif;');
    }

    /**
     * elseif.
     *
     * @param array $theme
     */
    public function elseifNodeCompiler(array &$theme)
    {
        $this->checkNode($theme);
        $attr = $this->getNodeAttribute($theme);

        $attr['condition'] = $this->parseContentIf($attr['condition'], false);
        $theme['content'] = $this->withPhpTag('elseif ('.$attr['condition'].'):');
    }

    /**
     * else.
     *
     * @param array $theme
     */
    public function elseNodeCompiler(array &$theme)
    {
        $this->checkNode($theme);
        $theme['content'] = $this->withPhpTag('else:');
    }

    /**
     * foreach list.
     *
     * @param array $theme
     */
    public function listNodeCompiler(array &$theme)
    {
        $this->checkNode($theme);
        $attr = $this->getNodeAttribute($theme);

        foreach ([
            'key',
            'value',
            'index',
        ] as $key) {
            null === $attr[$key] && $attr[$key] = '$'.$key;
        }

        foreach ([
            'for',
            'key',
            'value',
            'index',
        ] as $key) {
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
     * lists 增强版.
     *
     * @param array $theme
     */
    public function listsNodeCompiler(array &$theme)
    {
        $this->checkNode($theme);
        $attr = $this->getNodeAttribute($theme);

        null === $attr['index'] && $attr['index'] = 'index';
        null === $attr['key'] && $attr['key'] = 'key';
        null === $attr['id'] && $attr['id'] = 'id';
        null === $attr['mod'] && $attr['mod'] = 2;

        if (preg_match('/[^\\d-.,]/', (string) ($attr['mod']))) {
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

        $tmp .= PHP_EOL.'    if (count($tmp) == 0):'.PHP_EOL.
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
     * include.
     *
     * @param array $theme
     */
    public function includeNodeCompiler(array &$theme)
    {
        $this->checkNode($theme);
        $attr = $this->getNodeAttribute($theme);

        if (false === strpos($attr['file'], '(')) {
            // 后缀由主模板提供
            if (!$attr['ext'] && false !== strpos($attr['file'], '.')) {
                $temp = explode('.', $attr['file']);
                $attr['ext'] = '.'.array_pop($temp);
                $attr['file'] = implode('.', $temp);
            }

            if (0 !== strpos($attr['file'], '$')) {
                $attr['file'] = (0 === strpos($attr['file'], '$') ? '' : '\'').$attr['file'].'\'';
            }
        }

        $attr['file'] = $this->parseContentIf($attr['file'], false);

        $theme['content'] = $this->withPhpTag(
            '$this->display('.$attr['file'].
            ', [], \''.($attr['ext'] ?: '').
            '\', true);'
        );
    }

    /**
     * for.
     *
     * @param array $theme
     */
    public function forNodeCompiler(array &$theme)
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
     * while.
     *
     * @param array $theme
     */
    public function whileNodeCompiler(array &$theme)
    {
        $this->checkNode($theme);
        $attr = $this->getNodeAttribute($theme);

        $theme['content'] = $this->withPhpTag('while('.$attr['condition'].'):').
            $this->getNodeBody($theme).
            $this->withPhpTag('endwhile;');
    }

    /**
     * break.
     *
     * @param array $theme
     */
    public function breakNodeCompiler(array &$theme)
    {
        $this->checkNode($theme);
        $theme['content'] = $this->withPhpTag('break;');
    }

    /**
     * continue.
     *
     * @param array $theme
     */
    public function continueNodeCompiler(array &$theme)
    {
        $this->checkNode($theme);
        $theme['content'] = $this->withPhpTag('continue;');
    }

    /**
     * php.
     *
     * @param array $theme
     */
    public function phpNodeCompiler(array &$theme)
    {
        $this->checkNode($theme);
        $theme['content'] = $this->withPhpTag($this->getNodeBody($theme));
    }

    /**
     * 属性编译.
     *
     * @param array $theme
     */
    public function attributeNodeCompiler(array &$theme)
    {
        $source = trim($theme['content']);
        $source = $this->escapeRegexCharacter($source);

        if (true === $theme['is_js']) {
            $tag = $this->jsTag;
        } else {
            $tag = $this->nodeTag;
        }

        $allowedAttr = $tag[$theme['parent_name']]['attr'];

        // 正则匹配
        $regexp = [];

        if (!$theme['is_js']) {
            // xxx="yyy" 或 "yyy" 格式
            $regexp[] = '/(([^=\\s]+)=)?"([^"]+)"/';

            // xxx='yyy' 或 'yyy' 格式
            $regexp[] = "/(([^=\\s]+)=)?'([^\\']+)'/";
        }

        // xxx=yyy 或 yyy 格式
        $regexp[] = '/(([^=\\s]+)=)?([^\\s]+)/';

        $nameIdx = 2;
        $valueIdx = 3;
        $defaultIdx = 0;

        foreach ($regexp as $item) {
            if (preg_match_all($item, $source, $res)) {
                foreach ($res[0] as $idx => $attribute) {
                    $source = str_replace($attribute, '', $source);
                    $name = $res[$nameIdx][$idx];

                    if (empty($name)) {
                        $defaultIdx++;
                        $name = 'condition'.$defaultIdx;
                    }

                    $value = $res[$valueIdx][$idx];
                    $value = $this->escapeRegexCharacter($value, false);
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
     * 分析if.
     *
     * @param string $content
     * @param string $type
     *
     * @return string
     */
    protected function parseContentIf($content, $type = '')
    {
        $param = [];

        foreach (explode(' ', $content) as $value) {
            if (strpos($value, '.') > 0) {
                $args = explode('.', $value);
                $param[] = $args[0].($this->arrayHandler($args, true, 1));
            } else {
                $param[] = $value;
            }
        }

        $result = implode(' ', $param);

        if (false === $type) {
            return $result;
        }

        return $type."if ({$result})";
    }

    /**
     * 解析 JS 变量内容.
     *
     * @param string $content
     *
     * @return string
     */
    protected function parseJcontent($content)
    {
        $var = explode('|', $content);
        $content = array_shift($var);

        $content = $this->parseExpression($content);

        if (count($var) > 0) {
            return $this->parseJsFunction($content, $var);
        }

        return $content;
    }

    /**
     * 解析表达式语法.
     *
     * @param string $content
     *
     * @return string
     */
    protected function parseExpression($content)
    {
        $content = trim($content);

        $logic = [
            '+',
            '-',
            '.',
            '(',
            ')',
            '/',
            '%',
            '*',
            '?',
            ':',
            '<',
            '>',
            '=',
            '|',
            '&',
            '~',
            '!',
        ];

        $result = [];

        // 单逻辑
        $findLogic = false;

        for ($i = 0; $i < strlen($content); $i++) {
            $temp = $content[$i];

            if (0 === $i && $this->isVarExpression($temp)) {
                $result[] = '$';
            }

            if (in_array($temp, $logic, true)) {
                // . 语法作为对象连接符
                if ('.' === $temp &&
                    isset($content[$i + 1]) &&
                    $this->isVarExpression($content[$i + 1]) &&
                    !in_array($content[$i + 1], [' ', '$'], true)) {
                    $result[] = '->';
                    $findLogic = false;
                }

                // -> 语法原生对象连接符
                elseif ('>' === $temp &&
                    $i > 0 &&
                    '-' === $content[$i - 1] &&
                    isset($content[$i + 1]) &&
                    !in_array($content[$i + 1], [' ', '$'], true)) {
                    $result[] = '>';
                    $findLogic = false;
                } else {
                    $findLogic = true;
                    $result[] = $temp;
                }

                continue;
            }

            if (true === $findLogic && ' ' !== $temp) {
                if ($this->isVarExpression($temp)) {
                    $result[] = '$';
                }

                $findLogic = false;
            }

            $result[] = $temp;
        }

        $content = implode('', $result);

        // 还原函数去掉开头的美元符号
        $content = preg_replace_callback('/(\$+?[a-z0-9\\_]+?\\s*?)\\(.+?\\)/',
            function ($match) {
                return substr($match[0], 1);
            },
            $content
        );

        return $content;
    }

    /**
     * 是否为字符串表达式字符.
     *
     * @param string $char
     *
     * @return bool
     */
    protected function isVarExpression($char)
    {
        return !in_array($char, [
            '"',
            '\'',
            '(',
        ], true) && !is_numeric($char);
    }

    /**
     * 解析 JS 风格函数.
     *
     * @param string $name
     * @param array  $var
     *
     * @return string
     */
    protected function parseJsFunction($name, $var)
    {
        return $this->parseVarFunction($name, $var, true);
    }

    /**
     * 解析变量内容.
     *
     * @param string $content
     * @param bool   $isFunc
     *
     * @return string
     */
    protected function parseContent($content, bool $isFunc = true)
    {
        // 以|分割字符串,数组第一位是变量名字符串,之后的都是函数参数&&函数{$hello|md5}
        $var = explode('|', $content);

        // 弹出第一个元素,也就是变量名
        $tmp = array_shift($var);

        // 访问数组元素或者属性
        if (strpos($tmp, '.')) {
            $vars = explode('.', $tmp);

            // 这里 . 作为字符连接符
            if ("'" === ($firstLetter = substr($vars[1], 0, 1)) or
                '"' === $firstLetter or
                '$' === $firstLetter) {
                $name = '$'.$vars[0].'.'.$vars[1].($this->arrayHandler($vars, false));
            } else {
                $name = '$'.$vars[0].'->'.$vars[1].($this->arrayHandler($vars, true));
            }

            $tmp = $vars[0];
        }

        // $hello['demo'] 方式访问数组
        elseif (strpos($tmp, '[')) {
            $name = '$'.$tmp;
            preg_match('/(.+?)\[(.+?)\]/is', $tmp, $matches);
            $tmp = $matches[1];
        } else {
            $name = "\$${tmp}";
        }

        // 如果有使用函数
        if (true === $isFunc && count($var) > 0) {
            // 传入变量名,和函数参数继续解析,这里的变量名是上面的判断设置的值
            $name = $this->parseVarFunction($name, $var);
        }

        $name = str_replace('^', ':', $name);

        return $name ?: '';
    }

    /**
     * 解析函数.
     *
     * @param string $name
     * @param array  $var
     * @param bool   $isJavascript
     *
     * @return string
     */
    protected function parseVarFunction($name, $var, $isJavascript = false)
    {
        $len = count($var);

        for ($index = 0; $index < $len; $index++) {
            if (0 === stripos($var[$index], 'default=')) {
                $args = explode('=', $var[$index], 2);
            } else {
                $args = explode('=', $var[$index]);
            }

            $args[0] = trim($args[0]);

            if (false === $isJavascript && isset($args[1])) {
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
                            $name = "{$args[0]}(${name}, {$args[1]})";
                        }
                    } elseif (!empty($args[0])) {
                        $name = "{$args[0]}(${name})";
                    }
            }
        }

        return $name;
    }

    /**
     * 数组格式.
     *
     * @param array $vars
     * @param bool  $forObj
     * @param int   $start
     *
     * @return string
     */
    protected function arrayHandler(&$vars, bool $forObj = true, int $start = 2)
    {
        $len = count($vars);
        $param = '';

        for ($index = $start; $index < $len; $index++) {
            if (true === $forObj) {
                // 类似 $hello->test1->test2
                $param .= "->{$vars[$index]}";
            } else {
                // 类似 $hello.test1.test2
                $param .= ".{$vars[$index]}";
            }
        }

        return $param;
    }

    /**
     * 编码内容.
     *
     * @param string $content
     * @param string $content
     * @param mixed  $type
     *
     * @return string
     */
    protected function encodeContent($content, $type = '')
    {
        if ('global' === $type) {
            $content = Parser::globalEncode($content);
        } elseif (in_array($type, [
            'revert',
            'include',
        ], true)) {
            $content = base64_decode($content, true);
        } else {
            $content = Parser::revertEncode($content);
        }

        return $content;
    }

    /**
     * 验证节点是否正确.
     *
     * @param array $theme
     * @param bool  $jsNode
     *
     * @return bool
     */
    protected function checkNode(array $theme, bool $jsNode = false)
    {
        $attribute = $theme['children'][0];

        // 验证标签的属性值
        if (true !== $attribute['is_attribute']) {
            throw new InvalidArgumentException('Tag attribute type validation failed.');
        }

        // 验证必要属性
        $tag = true === $jsNode ? $this->jsTag : $this->nodeTag;

        if (!isset($tag[$theme['name']])) {
            throw new InvalidArgumentException(
                sprintf('The tag %s is undefined.', $theme['name'])
            );
        }

        foreach ($tag[$theme['name']]['required'] as $name) {
            $name = strtolower($name);

            if (!isset($attribute['attribute_list'][$name])) {
                throw new InvalidArgumentException(sprintf(
                    'The node %s lacks the required property: %s.', $theme['name'], $name)
                );
            }
        }

        return true;
    }

    /**
     * 取得节点的属性列表.
     *
     * @param array $theme 节点
     *
     * @return array
     */
    protected function getNodeAttribute($theme)
    {
        foreach ($theme['children'] as $child) {
            if (isset($child['is_attribute']) && true === $child['is_attribute']) {
                return $child['attribute_list'];
            }
        }

        return [];
    }

    /**
     * 取得body编译内容.
     *
     * @param array $theme 节点
     *
     * @return array
     */
    protected function getNodeBody($theme)
    {
        foreach ($theme['children'] as $child) {
            if (isset($child['is_body']) && 1 === $child['is_body']) {
                return $child['content'];
            }
        }
    }

    /**
     * 正则属性转义.
     *
     * @param string $txt
     * @param bool   $esc
     *
     * @return string
     */
    protected function escapeRegexCharacter($txt, bool $esc = true)
    {
        $txt = $this->escapeCharacter($txt, $esc);

        if (!$esc) {
            $txt = str_replace([
                ' band ',
                ' bxor ',
                ' bor ',
                ' bnot ',
                ' bleft ',
                ' bright ',
                ' and ',
                ' or ',
                ' not ',
                ' dot ',
                ' nheq ',
                ' heq ',
                ' neq ',
                ' eq ',
                ' egt ',
                ' gt ',
                ' elt ',
                ' lt ',
            ], [
                ' & ',
                ' ^ ',
                ' | ',
                ' ~ ',
                ' << ',
                ' >> ',
                ' && ',
                ' || ',
                ' != ',
                '->',
                ' !== ',
                ' === ',
                ' != ',
                ' == ',
                ' >= ',
                ' > ',
                ' <= ',
                ' < ',
            ], $txt);
        }

        return $txt;
    }

    /**
     * 正则属性转义.
     *
     * @param string $txt
     * @param bool   $esc
     *
     * @return string
     */
    protected function escapeCharacter($txt, bool $esc = true)
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
     *
     * @param array $content
     *
     * @return string
     */
    protected function withPhpTag($content)
    {
        return $this->phpTagStart().$content.$this->phpTagEnd();
    }

    /**
     * PHP 开始标签.
     *
     * @return string
     */
    protected function phpTagStart()
    {
        return '<?php ';
    }

    /**
     * PHP 结束标签.
     *
     * @return string
     */
    protected function phpTagEnd()
    {
        return ' ?>';
    }
}
