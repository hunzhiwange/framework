<?php declare(strict_types=1);
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
use Leevel\Option\TClass;
use Leevel\Support\Helper;

/**
 * 编译器列表
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2016.11.18
 * @version 1.0
 */
class Compiler implements ICompiler
{
    use TClass;

    /**
     * code 支持的特殊别名映射
     *
     * @var array
     */
    protected $arrCodeMap = [
        'php' => '~',
        'note' => '#',
        'variable' => '$',
        // foreach 和 for 冲突，foreach 改为 list
        'foreach' => 'list',
        'echo' => ':',
        'endtag' => [
            '/list',
            '/for',
            '/while',
            '/if',
            '/script',
            '/style'
        ]
    ];

    /**
     * node 支持的特殊别名映射
     *
     * @var array
     */
    protected $arrNodeMap = [];

    /**
     * js 风格支持的特殊别名映射
     *
     * @var array
     */
    protected $arrJsMap = [];

    /**
     * js 风格标签
     *
     * @var array
     */
    protected $arrJsTag = [
        // required 属性不能为空，single 单标签
        'if' => [
            'attr' => [
                'condition1'
            ],
            'single' => false,
            'required' => [
                'condition1'
            ]
        ],
        'elseif' => [
            'attr' => [
                'condition1'
            ],
            'single' => true,
            'required' => [
                'condition1'
            ]
        ],
        'else' => [
            'attr' => [],
            'single' => true,
            'required' => []
        ],
        'let' => [
            'attr' => [
                'condition1'
            ],
            'single' => true,
            'required' => [
                'condition1'
            ]
        ],
        'for' => [
            'attr' => [
                'condition1',
                'condition2',
                'condition3'
            ],
            'single' => false,
            'required' => [
                'condition1',
                'condition2',
                'condition3'
            ]
        ]
    ];

    /**
     * Node 标签
     *
     * @var array
     */
    protected $arrNodeTag = [
        // required 属性不能为空，single 单标签
        'assign' => [
            'attr' => [
                'name',
                'value'
            ],
            'single' => true,
            'required' => [
                'name'
            ]
        ],
        'if' => [
            'attr' => [
                'condition'
            ],
            'single' => false,
            'required' => [
                'condition'
            ]
        ],
        'elseif' => [
            'attr' => [
                'condition'
            ],
            'single' => true,
            'required' => [
                'condition'
            ]
        ],
        'else' => [
            'attr' => [],
            'single' => true,
            'required' => []
        ],
        'list' => [
            'attr' => [
                'for',
                'key',
                'value',
                'index'
            ],
            'single' => false,
            'required' => [
                'for'
            ]
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
                'id'
            ],
            'single' => false,
            'required' => [
                'name'
            ]
        ],
        'include' => [
            'attr' => [
                'file',
                'ext'
            ],
            'single' => true,
            'required' => [
                'file'
            ]
        ],
        'for' => [
            'attr' => [
                'step',
                'start',
                'end',
                'var',
                'type'
            ],
            'single' => false,
            'required' => []
        ],
        'while' => [
            'attr' => [
                'condition'
            ],
            'single' => false,
            'required' => [
                'condition'
            ]
        ],
        'break' => [
            'attr' => [],
            'single' => true,
            'required' => []
        ],
        'continue' => [
            'attr' => [],
            'single' => true,
            'required' => []
        ],
        'php' => [
            'attr' => [],
            'single' => false,
            'required' => []
        ]
    ];

    /**
     * 构造函数
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * 获取编译器
     *
     * @return array
     */
    public function getCompilers()
    {
        $arrMethods = get_class_methods($this);

        $arrCompilers = [];
        foreach ($arrMethods as $sMethod) {
            if (substr($sMethod, - 8) != 'Compiler') {
                continue;
            }

            $sMethod = substr($sMethod, 0, - 8);
            if (! in_array($sMethod, [
                'global',
                'jsvar',
                'globalrevert',
                'revert'
            ])) {
                $sType = strtolower(substr($sMethod, - 4));
                $sTag = substr($sMethod, 0, - 4);
                if ($sType == 'code') {
                    $mixName = $this->arrCodeMap[$sTag] ?? $sTag;
                } elseif ($sType == 'node') {
                    $mixName = $this->arrNodeMap[$sTag] ?? $sTag;
                } else {
                    $sType = strtolower(substr($sMethod, - 2));
                    $sTag = substr($sMethod, 0, - 2);
                    $mixName = $this->arrJsMap[$sTag] ?? $sTag;
                }
                $arrCompilers[] = [
                    $sType,
                    $mixName,
                    $sTag
                ];
            }
        }

        unset($arrMethods);

        return $arrCompilers;
    }

    /**
     * node.tag
     *
     * @return array
     */
    public function getNodeTagHelp()
    {
        return $this->arrNodeTag;
    }

    /**
     * js.tag
     *
     * @return array
     */
    public function getJsTagHelp()
    {
        return $this->arrJsTag;
    }

    /**
     * 全局编译器（保护标签）
     *
     * @param array $theme
     * @return void
     */
    public function globalCompiler(&$theme)
    {
        $theme['content'] = $this->encodeContent($theme['content'], 'global');
    }

    /**
     * 全局还原编译器（保护标签还原）
     *
     * @param array $theme
     * @return void
     */
    public function globalrevertCompiler(&$theme)
    {
        $theme['content'] = $this->encodeContent($theme['content'], 'revert');
    }

    /**
     * node.code 还原编译器
     *
     * @param array $theme
     * @return void
     */
    public function revertCompiler(&$theme)
    {
        $theme['content'] = $this->encodeContent($theme['content'], 'revert');
    }

    /**
     * 变量
     *
     * @param array $theme
     * @return void
     */
    public function variableCodeCompiler(&$theme)
    {
        $theme['content'] = ! empty($theme['content']) ? $this->parseContent($theme['content']) : null;

        if ($theme['content'] !== null) {
            $theme['content'] = $this->withPhpTag('echo ' . $theme['content'] . ';');
        }

        $theme['content'] = $this->encodeContent($theme['content']);
    }

    /**
     * if
     *
     * @param array $theme
     * @return void
     */
    public function ifCodeCompiler(&$theme)
    {
        $theme['content'] = $this->parseContentIf($theme['content']);
        $theme['content'] = $this->encodeContent(
            $this->withPhpTag($theme['content'] . ':')
        );
    }

    /**
     * elseif
     *
     * @param array $theme
     * @return void
     */
    public function elseifCodeCompiler(&$theme)
    {
        $theme['content'] = $this->parseContentIf($theme['content'], 'else');
        $theme['content'] = $this->encodeContent(
            $this->withPhpTag($theme['content'] . ':')
        );
    }

    /**
     * else 标签
     *
     * @param array $theme
     * @return void
     */
    public function elseCodeCompiler(&$theme)
    {
        $theme['content'] = $this->encodeContent(
            $this->withPhpTag('else:')
        );
    }

    /**
     * foreach
     *
     * @param array $theme
     * @return void
     */
    public function foreachCodeCompiler(&$theme)
    {
        // 分析 foreach
        $calHelp = function ($content) {
            preg_match_all('/\\$([\S]+)/', $content, $arrArray);

            $arrArray = $arrArray[1];
            $nNum = count($arrArray);
            if ($nNum > 0) {
                if ($nNum == 2) {
                    $sResult = "\${$arrArray[1]}";
                } elseif ($nNum == 3) {
                    $sResult = "\${$arrArray[1]} => \${$arrArray[2]}";
                } else {
                    throw new InvalidArgumentException('The parameter of code.foreach tag can be at most three.');
                }

                return "if (is_array(\${$arrArray[0]})): foreach(\${$arrArray[0]} as {$sResult})";
            }
        };

        $theme['content'] = $calHelp($theme['content']);
        $theme['content'] = $this->encodeContent(
            $this->withPhpTag($theme['content'] . ':')
        );
    }

    /**
     * for
     *
     * @param array $theme
     * @return void
     */
    public function forCodeCompiler(&$theme)
    {
        $theme['content'] = $this->encodeContent(
            $this->withPhpTag('for (' . $theme['content'] . '):')
        );
    }

    /**
     * while 头部
     *
     * @param array $theme
     * @return void
     */
    public function whileCodeCompiler(&$theme)
    {
        $theme['content'] = $this->encodeContent(
            $this->withPhpTag('while (' . $theme['content'] . '):')
        );
    }

    /**
     * php 脚本
     *
     * @param array $theme
     * @return void
     */
    public function phpCodeCompiler(&$theme)
    {
        $theme['content'] = $this->encodeContent(
            $this->withPhpTag($theme['content'] . ';')
        );
    }

    /**
     * 注释
     *
     * @param array $theme
     * @return void
     */
    public function noteCodeCompiler(&$theme)
    {
        $theme['content'] = $this->encodeContent(' ');
    }

    /**
     * PHP echo 标签
     *
     * @param array $theme
     * @return void
     */
    public function echoCodeCompiler(&$theme)
    {
        $theme['content'] = $this->encodeContent(
            $this->withPhpTag('echo ' . $theme['content'] . ';')
        );
    }

    /**
     * javascript 初始标签
     *
     * @param array $theme
     * @return void
     */
    public function scriptCodeCompiler(&$theme)
    {
        $theme['content'] = $this->encodeContent(
            '<script type="text/javascript">'
        );
    }

    /**
     * css 初始标签
     *
     * @param array $theme
     * @return void
     */
    public function styleCodeCompiler(&$theme)
    {
        $theme['content'] = $this->encodeContent('<style type="text/css">');
    }

    /**
     * endtag
     *
     * @param array $theme
     * @return void
     */
    public function endtagCodeCompiler(&$theme)
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
     * 变量及表达式
     *
     * @param array $theme
     * @return void
     */
    public function jsvarCompiler(&$theme)
    {
        $theme['content'] = $this->withPhpTag(
            'echo ' . $this->parseJcontent($theme['content']) . ';'
        );
    }

    /**
     * let 编译器
     *
     * @param array $theme
     * @return void
     */
    public function letJsCompiler(&$theme)
    {
        $this->checkNode($theme, true);
        $attr = $this->getNodeAttribute($theme);
        $name = array_shift($attr);
        $equal = array_shift($attr);

        if ($equal != '=') {
            array_unshift($attr, $equal);
        }

        if (! $attr) {
            $value = 'null'; 
        } else {
            $value = $this->parseExpression(implode(' ', $attr));
        }

        $theme['content'] = $this->withPhpTag("\${$name} = " . $value . ';');
    }

    /**
     * if 编译器
     *
     * @param array $theme
     * @return void
     */
    public function ifJsCompiler(&$theme)
    {
        $this->checkNode($theme, true);
        $attr = $this->getNodeAttribute($theme);
        $attr = $this->parseExpression(implode(' ', $attr));

        $theme['content'] = $this->withPhpTag("if ({$attr}):");
        $theme['content'] .= $this->getNodeBody($theme);
        $theme['content'] .= $this->withPhpTag('endif;');
    }

    /**
     * elseif 编译器
     *
     * @param array $theme
     * @return void
     */
    public function elseifJsCompiler(&$theme)
    {
        $this->checkNode($theme, true);
        $attr = $this->getNodeAttribute($theme);
        $attr = $this->parseExpression(implode(' ', $attr));

        $theme['content'] = $this->withPhpTag("elseif ({$attr}):");
        $theme['content'] .= $this->getNodeBody($theme);
    }

    /**
     * else
     *
     * @param array $theme
     * @return void
     */
    public function elseJsCompiler(&$theme)
    {
        $theme['content'] = $this->withPhpTag('else:');
    }

    /**
     * for 循环
     *
     * @param array $theme
     * @return void
     */
    public function forJsCompiler(&$theme)
    {
        $this->checkNode($theme, true);
        $attr = $this->getNodeAttribute($theme);

        if (! in_array('in', $attr)) {
            throw new InvalidArgumentException('For tag need in separate.');
        }

        $key = 'key';
        $value = array_shift($attr);
        $next = array_shift($attr);

        if ($next == ',') {
            $key = $value;
            $value = array_shift($attr);
        } elseif ($next != 'in') {
            $key = $value;
            $value = $next;
        }

        array_shift($attr);

        if (! $attr) {
            throw new InvalidArgumentException('For tag need a var to be circulate.');
        }

        $attr = $this->parseExpression(implode(' ', $attr));

        $theme['content'] = $this->withPhpTag("foreach ({$attr} as \${$key} => \${$value}):");
        $theme['content'] .= $this->getNodeBody($theme);
        $theme['content'] .= $this->withPhpTag('endforeach;');
    }

    /**
     * assign
     *
     * @param array $theme
     * @return void
     */
    public function assignNodeCompiler(&$theme)
    {
        $this->checkNode($theme);
        $attr = $this->getNodeAttribute($theme);

        $attr['name'] = $this->parseContent($attr['name'], false);
        if ($attr['value'] === null) {
            $attr['value'] = '';
        } else {
            if ('$' == substr($attr['value'], 0, 1)) {
                $attr['value'] = $this->parseContent(substr($attr['value'], 1));
            } else {
                $attr['value'] = '\'' . $attr['value'] . '\'';
            }
        }

        // 编译
        $theme['content'] = $this->withPhpTag(
            $attr['name'] . '=' . $attr['value'] . ';'
        );
    }

    /**
     * if
     *
     * @param array $theme
     * @return void
     */
    public function ifNodeCompiler(&$theme)
    {
        $this->checkNode($theme);
        $attr = $this->getNodeAttribute($theme);

        $attr['condition'] = $this->parseConditionHelp($attr['condition']);

        $theme['content'] = $this->withPhpTag('if (' . $attr['condition'] . '):') .
            $this->getNodeBody($theme) .
            $this->withPhpTag('endif;');
    }

    /**
     * elseif
     *
     * @param array $theme
     * @return void
     */
    public function elseifNodeCompiler(&$theme)
    {
        $this->checkNode($theme);
        $attr = $this->getNodeAttribute($theme);

        //$attr['condition'] = str_replace('.', '->', $attr['condition']);
        $attr['condition'] = $this->parseConditionHelp($attr['condition']);

        $theme['content'] = $this->withPhpTag('elseif (' . $attr['condition'] . '):');
    }

    /**
     * else
     *
     * @param array $theme
     * @return void
     */
    public function elseNodeCompiler(&$theme)
    {
        $this->checkNode($theme);
        $theme['content'] = $this->withPhpTag('else:');
    }

    /**
     * foreach list
     *
     * @param array $theme
     * @return void
     */
    public function listNodeCompiler(&$theme)
    {
        $this->checkNode($theme);
        $attr = $this->getNodeAttribute($theme);

        foreach ([
            'key',
            'value',
            'index'
        ] as $sKey) {
            $attr[$sKey] === null && $attr[$sKey] = '$' . $sKey;
        }

        foreach ([
            'for',
            'key',
            'value',
            'index'
        ] as $sKey) {
            if ('$' . $sKey == $attr[$sKey]) {
                continue;
            }

            $attr[$sKey] = $this->parseContent($attr[$sKey]);
        }

        // 编译
        $theme['content'] = $this->withPhpTag($attr['index'] . ' = 1;') . PHP_EOL .
            $this->withPhpTag(
                'if (is_array(' . $attr['for'] . ')): foreach (' .$attr['for'] .
                ' as ' . $attr['key'] . ' => ' .
                $attr['value'] . '):'
            ) .
            $this->getNodeBody($theme) .
            $this->withPhpTag($attr['index'] . '++;') . PHP_EOL .
            $this->withPhpTag('endforeach; endif;');
    }

    /**
     * lists 增强版
     *
     * @param array $theme
     * @return void
     */
    public function listsNodeCompiler(&$theme)
    {
        $this->checkNode($theme);
        $attr = $this->getNodeAttribute($theme);

        $attr['index'] === null && $attr['index'] = 'index';
        $attr['key'] === null && $attr['key'] = 'key';
        $attr['id'] === null && $attr['id'] = 'id';
        $attr['mod'] === null && $attr['mod'] = 2;

        if (preg_match("/[^\d-.,]/", $attr['mod'])) {
            $attr['mod'] = '$' . $attr['mod'];
        }

        $attr['empty'] === null && $attr['empty'] = '';
        $attr['length'] === null && $attr['length'] = '';
        $attr['offset'] === null && $attr['offset'] = '';
        $attr['name'] = $this->parseContent($attr['name']);

        $compiled = [];
        $compiled[] = $this->phpTagStart() . 'if (is_array(' .
            $attr['name'] . ')): $' .
            $attr['index'] . ' = 0;';

        if ('' != $attr['length']) {
            $compiled[] = '$arrList = array_slice(' .
                $attr['name'] . ', ' .
                $attr['offset'] . ', ' .
                $attr['length'] . ');';
        } elseif ('' != $attr['offset']) {
            $compiled[] = '$arrList = array_slice(' .
                $attr['name'] . ', ' .
                $attr['offset'] . ');';
        } else {
            $compiled[] = '$arrList = ' . $attr['name'] . ';';
        }

        $compiled[] = 'if (count($arrList) == 0): echo  "' .$attr['empty'] . '";';
        $compiled[] = 'else:';
        $compiled[] = 'foreach ($arrList as $' . $attr['key'] .
            ' => $' . $attr['id'] . '):';
        $compiled[] = '++$' . $attr['index'] . ';';
        $compiled[] = '$mod = $' . $attr['index'] . ' % ' .
            $attr['mod'] . ';' . $this->phpTagEnd();

        $compiled[] = $this->getNodeBody($theme);
        $compiled[] = $this->withPhpTag(
            'endforeach; endif; else: echo "' .
            $attr['empty'] . '"; endif;'
        );

        $theme['content'] = implode(' ', $compiled);
    }

    /**
     * include
     *
     * @param array $theme
     * @return void
     */
    public function includeNodeCompiler(&$theme)
    {
        $this->checkNode($theme);
        $attr = $this->getNodeAttribute($theme);
        $attr['file'] = str_replace('->', '.', $attr['file']);

        // 后缀由主模板提供
        if (! $attr['ext'] && strpos($attr['file'], '.') !== false) {
            $temp = explode('.', $attr['file']);
            $attr['ext'] = '.' . array_pop($temp);
            $attr['file'] = implode('.', $temp);
        }

        if (strpos($attr['file'], '$') !== 0 && strpos($attr['file'], '(') === false) {
            $attr['file'] = (strpos($attr['file'], '$') === 0 ? '' : '\'') . $attr['file'] . '\'';
        }

        $theme['content'] = $this->withPhpTag(
            '$this->display(' . $attr['file'] .
            ', [], \'' . ($attr['ext'] ?  : '') .
            '\', true);'
        );
    }

    /**
     * for
     *
     * @param array $theme
     * @return void
     */
    public function forNodeCompiler(&$theme)
    {
        $this->checkNode($theme);
        $attr = $this->getNodeAttribute($theme);

        $attr['step'] === null && $attr['step'] = '1';
        $attr['start'] === null && $attr['start'] = '0';
        $attr['end'] === null && $attr['end'] = '0';
        $attr['var'] === null && $attr['var'] = 'var';
        $attr['var'] = '$' . $attr['var'];
        if ($attr['type'] == '-') {
            $sComparison = ' >= ';
            $sMinusPlus = ' -= ';
        } else {
            $sComparison = ' <= ';
            $sMinusPlus = ' += ';
        }

        $compiled = [];
        $compiled[] = $this->withPhpTag(
            'for (' . $attr['var'] .
            ' = ' . $attr['start'] . '; ' .
            $attr['var'] . $sComparison . $attr['end'] . '; ' .
            $attr['var'] . $sMinusPlus . $attr['step'] . '):'
        );
        $compiled[] = $this->getNodeBody($theme);
        $compiled[] = $this->withPhpTag('endfor;');

        $theme['content'] = implode('', $compiled);
    }

    /**
     * while
     *
     * @param array $theme
     * @return void
     */
    public function whileNodeCompiler(&$theme)
    {
        $this->checkNode($theme);
        $attr = $this->getNodeAttribute($theme);

        $theme['content'] = $this->withPhpTag('while(' . $attr['condition'] . '):') .
            $this->getNodeBody($theme) .
            $this->withPhpTag('endwhile;');
    }

    /**
     * break
     *
     * @param array $theme
     * @return void
     */
    public function breakNodeCompiler(&$theme)
    {
        $this->checkNode($theme);
        $theme['content'] = $this->withPhpTag('break;');
    }

    /**
     * continue
     *
     * @param array $theme
     * @return void
     */
    public function continueNodeCompiler(&$theme)
    {
        $this->checkNode($theme);
        $theme['content'] = $this->withPhpTag('continue;');
    }

    /**
     * php
     *
     * @param array $theme
     * @return void
     */
    public function phpNodeCompiler(&$theme)
    {
        $this->checkNode($theme);
        $theme['content'] = $this->withPhpTag($this->getNodeBody($theme));
    }

    /**
     * 属性编译
     *
     * @param array $theme
     * @return void
     */
    public function attributeNodeCompiler(&$theme)
    {
        $sSource = trim($theme['content']);
        $this->escapeRegexCharacter($sSource);

        if ($theme['is_js'] === true) {
            $arrTag = $this->arrJsTag;
        } else {
            $arrTag = $this->arrNodeTag;
        }

        $arrAllowedAttr = $arrTag[$theme['parent_name']]['attr'];

        // 正则匹配
        $arrRegexp = [];
        if (! $theme['is_js']) {
            $arrRegexp[] = "/(([^=\s]+)=)?\"([^\"]+)\"/"; // xxx="yyy" 或 "yyy" 格式
            $arrRegexp[] = "/(([^=\s]+)=)?'([^\']+)'/"; // xxx='yyy' 或 'yyy' 格式
        }
        $arrRegexp[] = "/(([^=\s]+)=)?([^\s]+)/"; // xxx=yyy 或 yyy 格式
        
        $nNameIdx = 2;
        $nValueIdx = 3;
        $nDefaultIdx = 0;

        foreach ($arrRegexp as $sRegexp) {
            if (preg_match_all($sRegexp, $sSource, $arrRes)) {
                foreach ($arrRes[0] as $nIdx => $sAttribute) {
                    $sSource = str_replace($sAttribute, '', $sSource);
                    $sName = $arrRes[$nNameIdx][$nIdx];
                    if (empty($sName)) {
                        $nDefaultIdx ++;
                        $sName = 'condition' . $nDefaultIdx;
                    }

                    $sValue = $arrRes[$nValueIdx][$nIdx];
                    $this->escapeRegexCharacter($sValue, false);
                    $theme['attribute_list'][strtolower($sName)] = $sValue;
                }
            }
        }

        // 补全节点其余参数
        foreach ($arrAllowedAttr as $str) {
            if (! isset($theme['attribute_list'][$str])) {
                $theme['attribute_list'][$str] = null;
            }
        }

        $theme['content'] = $sSource;
    }

    /**
     * 分析if
     *
     * @param string $content
     * @param string $sType
     * @return string
     */
    protected function parseContentIf($content, $sType = '')
    {
        $arrArray = explode(' ', $content);
        $bObj = false;
        $arrParam = [];
        foreach ($arrArray as $sV) {
            if (strpos($sV, '.') > 0) {
                $arrArgs = explode('.', $sV);

                // 以$hello['hello1']['hello2']['hello2']方式
                $arrParam[] = $arrArgs[0] . ($this->arrayHandler($arrArgs, 1, 1));

                // 以$hello->'hello1->'hello2'->'hello2'方式
                $arrParamTwo[] = $arrArgs[0] . ($this->arrayHandler($arrArgs, 2, 1)); 

                $bObj = true;
            } else {
                $arrParam[] = $sV;
                $arrParamTwo[] = $sV;
            }
        }

        if ($bObj) {
            $sStr = 'is_array(' . $arrArgs[0] . ')' . '?' . join(' ', $arrParam) . ':' . join(' ', $arrParamTwo);
        } else {
            $sStr = join(' ', $arrParam);
        }

        //$sStr = str_replace(':', '->', $sStr);
        //$sStr = str_replace('+', '::', $sStr);

        return $sType . "if ({$sStr})";
    }

    /**
     * 解析 JS 变量内容
     *
     * @param string $content
     * @return string
     */
    protected function parseJcontent($content)
    {
        $arrVar = explode('|', $content);
        $content = array_shift($arrVar);

        $content = $this->parseExpression($content);

        if (count($arrVar) > 0) {
            return $this->parseJsFunction($content, $arrVar);
        }else {
            return $content;
        }
    }

    /**
     * 解析表达式语法
     *
     * @param string $content
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
            '!'
        ];

        $result = [];

        // 单逻辑
        $findLogic = false;
        for ($i=0; $i<strlen($content); $i++) {
            $temp = $content{$i};

            if ($i === 0 && $this->isVarExpression($temp)) {
               $result[] = '$';
            }

            if (in_array($temp, $logic)) {
                $findLogic = true;
                $result[] = $temp;
                continue;
            }

            if ($findLogic === true && $temp != ' ') {
                if ($this->isVarExpression($temp)) {
                    $result[] = '$';
                }
                $findLogic = false;
            }

            $result[] = $temp;
        }

        $content = implode('',$result);

        // 还原函数去掉开头的美元符号
        $content = preg_replace_callback("/(\\$+?[a-z0-9\_]+?\s*?)\(.+?\)/", function ($match) {
            return substr($match[0], 1);
        }, $content);

        return $content;
    }

    /**
     * 是否为字符串表达式字符
     *
     * @param string $char
     * @return boolean
     */
    protected function isVarExpression($char)
    {
        return ! in_array($char, [
            '"',
            '\'',
            '('
        ]) && ! is_numeric($char);
    }

    /**
     * 解析 JS 风格函数
     *
     * @param string $sName
     * @param array $arrVar
     * @return string
     */
    protected function parseJsFunction($sName, $arrVar)
    {
        return $this->parseVarFunction($sName, $arrVar, true);
    }

    /**
     * 解析变量内容
     *
     * @param string $content
     * @param bool $booFunc 是否允许解析函数
     * @return string
     */
    protected function parseContent($content, $booFunc = true)
    {
        //$content = str_replace(':', '->', $content); // 以|分割字符串,数组第一位是变量名字符串,之后的都是函数参数&&函数{$hello|md5}

        $arrVar = explode('|', $content);

        // 弹出第一个元素,也就是变量名
        $sVar = array_shift($arrVar); 

        // 访问数组元素或者属性
        if (strpos($sVar, '.')) { 
            $arrVars = explode('.', $sVar);

            // 这里 . 作为字符连接符
            if (($firstLetter = substr($arrVars[1], 0, 1)) == "'" or 
                $firstLetter == '"' or 
                $firstLetter == "$") {
                $sName = '$' . $arrVars[0] . '.' . $arrVars[1] . ($this->arrayHandler($arrVars, 3));
            } else {
                $sName = '$' . $arrVars[0] . '->' . $arrVars[1] . ($this->arrayHandler($arrVars, 2));
            }

            $sVar = $arrVars[0];
        }

        // $hello['demo'] 方式访问数组
        elseif (strpos($sVar, '[')) {
            $sName = "$" . $sVar;
            preg_match('/(.+?)\[(.+?)\]/is', $sVar, $arrArray);
            $sVar = $arrArray[1];
        } else {
            $sName = "\$$sVar";
        }

        // 如果有使用函数
        if ($booFunc === true && count($arrVar) > 0) {

            // 传入变量名,和函数参数继续解析,这里的变量名是上面的判断设置的值
            $sName = $this->parseVarFunction($sName, $arrVar);
        }

        $sName = str_replace('^', ':', $sName);

        return $sName ?: '';
    }

    /**
     * 解析函数
     *
     * @param string $sName
     * @param array $arrVar
     * @param boolean $bJs 是否为 JS 风格变量解析
     * @return string
     */
    protected function parseVarFunction($sName, $arrVar, $bJs = false)
    {
        $nLen = count($arrVar);

        for ($nI = 0; $nI < $nLen; $nI ++) {
            if (0 === stripos($arrVar[$nI], 'default=')) {
                $arrArgs = explode('=', $arrVar[$nI], 2);
            } else {
                $arrArgs = explode('=', $arrVar[$nI]);
            }

            $arrArgs[0] = trim($arrArgs[0]);

            if ($bJs === false) {
                //$arrArgs[0] = str_replace('+', '::', $arrArgs[0]);
                if (isset($arrArgs[1])) {
                    $arrArgs[1] = str_replace('->', ':', $arrArgs[1]);
                }
            }

            switch (strtolower($arrArgs[0])) {

                // 特殊模板函数
                case 'default': 
                    $sName = $sName . ' ?: ' . $arrArgs[1];
                    break;

                // 通用模板函数
                default: 
                    if (isset($arrArgs[1])) {
                        if (strstr($arrArgs[1], '**')) {
                            $arrArgs[1] = str_replace('**', $sName, $arrArgs[1]);
                            $sName = "$arrArgs[0]($arrArgs[1])";
                        } else {
                            $sName = "$arrArgs[0]($sName, $arrArgs[1])";
                        }
                    } elseif (! empty($arrArgs[0])) {
                        $sName = "$arrArgs[0]($sName)";
                    }
            }
        }

        return $sName;
    }

    /**
     * 转换对象方法和静态方法连接符
     *
     * @param string $content
     * @return string
     */
    protected function parseConditionHelp($content)
    {
        return $content;
        return str_replace([
            ':',
            '+'
        ], [
            '->',
            '::'
        ], $content);
    }

    /**
     * 数组格式
     *
     * @param array $arrVars
     * @param number $nType
     * @param number $nGo
     * @return string
     */
    protected function arrayHandler(&$arrVars, $nType = 1, $nGo = 2)
    {
        $nLen = count($arrVars);

        $sParam = '';

        // 类似 $hello['test']['test2']
        if ($nType == 1) {
            for ($nI = $nGo; $nI < $nLen; $nI ++) {
                $sParam .= "['{$arrVars[$nI]}']";
            }
        } 

        // 类似 $hello->test1->test2
        elseif ($nType == '2') { 
            for ($nI = $nGo; $nI < $nLen; $nI ++) {
                $sParam .= "->{$arrVars[$nI]}";
            }
        } 

        // 类似 $hello.test1.test2
        elseif ($nType == '3') { 
            for ($nI = $nGo; $nI < $nLen; $nI ++) {
                $sParam .= ".{$arrVars[$nI]}";
            }
        }

        return $sParam;
    }

    /**
     * 编码内容
     *
     * @param string $content
     * @param string $content
     * @return string
     */
    protected function encodeContent($content, $sType = '')
    {
        if ($sType == 'global') {
            $content = parser::globalEncode($content);
        } elseif (in_array($sType, [
            'revert',
            'include'
        ])) {
            $content = base64_decode($content);
        } else {
            $content = parser::revertEncode($content);
        }
        return $content;
    }

    /**
     * 验证节点是否正确
     *
     * @param array $theme
     * @return boolean
     */
    protected function checkNode($theme, $bJsNode = false)
    {
        $attribute = $theme['children'][0];

        // 验证标签的属性值
        if ($attribute['is_attribute'] !== true) {
            throw new InvalidArgumentException('Tag attribute type validation failed.');
        }

        // 验证必要属性
        $arrTag = $bJsNode === true ? $this->arrJsTag : $this->arrNodeTag;
        if (! isset($arrTag[$theme['name']])) {
            throw new InvalidArgumentException(sprintf('The tag %s is undefined.', $theme['name']));
        }

        foreach ($arrTag[$theme['name']]['required'] as $sName) {
            $sName = strtolower($sName);
            if (! isset($attribute['attribute_list'][$sName])) {
                throw new InvalidArgumentException(sprintf('The node %s lacks the required property: %s.', $theme['name'], $sName));
            }
        }

        return true;
    }

    /**
     * 取得节点的属性列表
     *
     * @param array $theme 节点
     * @return array
     */
    protected function getNodeAttribute($theme)
    {
        foreach ($theme['children'] as $arrChild) {
            if (isset($arrChild['is_attribute']) && $arrChild['is_attribute'] == 1) {
                return $arrChild['attribute_list'];
            }
        }
        return [];
    }

    /**
     * 取得body编译内容
     *
     * @param array $theme 节点
     * @return array
     */
    protected function getNodeBody($theme)
    {
        foreach ($theme['children'] as $arrChild) {
            if (isset($arrChild['is_body']) && $arrChild['is_body'] == 1) {
                return $arrChild['content'];
            }
        }
        return null;
    }

    /**
     * 正则属性转义
     *
     * @param string $sTxt
     * @param bool $bEsc
     * @return string
     */
    protected function escapeRegexCharacter(&$sTxt, $bEsc = true)
    {
        $sTxt = $this->escapeCharacter($sTxt, $bEsc);

        if (! $bEsc) {
            $sTxt = str_replace([
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
                ' lt ' 
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
                ' < '
            ], $sTxt);
        }

        return $sTxt;
    }

    /**
     * 正则属性转义
     *
     * @param string $sTxt
     * @param bool $bEsc
     * @return string
     */
    protected function escapeCharacter($sTxt, $bEsc = true)
    {
        if ($sTxt == '""') {
            $sTxt = '';
        }

        if ($bEsc) {
            // 转义
            $sTxt = str_replace([
                '\\\\',
                "\\'",
                '\\"',
                '\\$',
                '\\.'
            ], [
                '\\',
                '~~{#!`!#}~~',
                '~~{#!``!#}~~',
                '~~{#!S!#}~~',
                '~~{#!dot!#}~~'
            ], $sTxt);
        } else {
            // 还原
            $sTxt = str_replace([
                '.',
                "~~{#!`!#}~~",
                '~~{#!``!#}~~',
                '~~{#!S!#}~~',
                '~~{#!dot!#}~~'
            ], [
                '->',
                "'",
                '"',
                '$',
                '.'
            ], $sTxt);
        }

        return $sTxt;
    }

    /**
     * PHP 标签包裹内容
     *
     * @param array $content
     * @return string
     */
    protected function withPhpTag($content)
    {   
        return $this->phpTagStart() . $content . $this->phpTagEnd();
    }

    /**
     * PHP 开始标签
     *
     * @return string
     */
    protected function phpTagStart()
    {
        return '<?' . 'php ';
    }

    /**
     * PHP 结束标签
     *
     * @return string
     */
    protected function phpTagEnd()
    {
        return '?' . '>';
    }
}
