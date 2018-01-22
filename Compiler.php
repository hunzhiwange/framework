<?php
/*
 * This file is part of the ************************ package.
 * ##########################################################
 * #   ____                          ______  _   _ ______   #
 * #  /     \       ___  _ __  _   _ | ___ \| | | || ___ \  #
 * # |   (  ||(_)| / _ \| '__|| | | || |_/ /| |_| || |_/ /  #
 * #  \____/ |___||  __/| |   | |_| ||  __/ |  _  ||  __/   #
 * #       \__   | \___ |_|    \__  || |    | | | || |      #
 * #     Query Yet Simple      __/  |\_|    |_| |_|\_|      #
 * #                          |___ /  Since 2010.10.03      #
 * ##########################################################
 *
 * The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Queryyetsimple\View;

use InvalidArgumentException;
use Queryyetsimple\Option\TClass;
use Queryyetsimple\Support\Helper;

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
        'foreach' => 'list',  // foreach 和 for 冲突，foreach 改为 list
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
     * @param array $arrTheme
     * @return void
     */
    public function globalCompiler(&$arrTheme)
    {
        $arrTheme['content'] = $this->encodeContent($arrTheme['content'], 'global');
    }

    /**
     * 全局还原编译器（保护标签还原）
     *
     * @param array $arrTheme
     * @return void
     */
    public function globalrevertCompiler(&$arrTheme)
    {
        $arrTheme['content'] = $this->encodeContent($arrTheme['content'], 'revert');
    }

    /**
     * node.code 还原编译器
     *
     * @param array $arrTheme
     * @return void
     */
    public function revertCompiler(&$arrTheme)
    {
        $arrTheme['content'] = $this->encodeContent($arrTheme['content'], 'revert');
    }

    /**
     * 变量
     *
     * @param array $arrTheme
     * @return void
     */
    public function variableCodeCompiler(&$arrTheme)
    {
        $arrTheme['content'] = ! empty($arrTheme['content']) ? $this->parseContent($arrTheme['content']) : null;
        if ($arrTheme['content'] !== null) {
            $arrTheme['content'] = $this->phpTagStart() . 'echo ' . $arrTheme['content'] . ';' . $this->phpTagEnd();
        }
        $arrTheme['content'] = $this->encodeContent($arrTheme['content']);
    }

    /**
     * if
     *
     * @param array $arrTheme
     * @return void
     */
    public function ifCodeCompiler(&$arrTheme)
    {
        $arrTheme['content'] = $this->parseContentIf($arrTheme['content']);
        $arrTheme['content'] = $this->encodeContent($this->phpTagStart() . $arrTheme['content'] . ':' . $this->phpTagEnd());
    }

    /**
     * elseif
     *
     * @param array $arrTheme
     * @return void
     */
    public function elseifCodeCompiler(&$arrTheme)
    {
        $arrTheme['content'] = $this->parseContentIf($arrTheme['content'], 'else');
        $arrTheme['content'] = $this->encodeContent($this->phpTagStart() . $arrTheme['content'] . ':' . $this->phpTagEnd());
    }

    /**
     * else 标签
     *
     * @param array $arrTheme
     * @return void
     */
    public function elseCodeCompiler(&$arrTheme)
    {
        $arrTheme['content'] = $this->encodeContent($this->phpTagStart() . 'else:' . $this->phpTagEnd());
    }

    /**
     * foreach
     *
     * @param array $arrTheme
     * @return void
     */
    public function foreachCodeCompiler(&$arrTheme)
    {
        // 分析 foreach
        $calHelp = function ($sContent) {
            preg_match_all('/\\$([\S]+)/', $sContent, $arrArray);

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

        $arrTheme['content'] = $calHelp($arrTheme['content']);
        $arrTheme['content'] = $this->encodeContent($this->phpTagStart() . $arrTheme['content'] . ':' . $this->phpTagEnd());
    }

    /**
     * for
     *
     * @param array $arrTheme
     * @return void
     */
    public function forCodeCompiler(&$arrTheme)
    {
        $arrTheme['content'] = $this->encodeContent($this->phpTagStart() . 'for(' . $arrTheme['content'] . '):' . $this->phpTagEnd());
    }

    /**
     * while 头部
     *
     * @param array $arrTheme
     * @return void
     */
    public function whileCodeCompiler(&$arrTheme)
    {
        $arrTheme['content'] = $this->encodeContent($this->phpTagStart() . 'while(' . $arrTheme['content'] . '):' . $this->phpTagEnd());
    }

    /**
     * php 脚本
     *
     * @param array $arrTheme
     * @return void
     */
    public function phpCodeCompiler(&$arrTheme)
    {
        $arrTheme['content'] = $this->encodeContent($this->phpTagStart() . $arrTheme['content'] . ';' . $this->phpTagEnd());
    }

    /**
     * 注释
     *
     * @param array $arrTheme
     * @return void
     */
    public function noteCodeCompiler(&$arrTheme)
    {
        $arrTheme['content'] = $this->encodeContent(' ');
    }

    /**
     * PHP echo 标签
     *
     * @param array $arrTheme
     * @return void
     */
    public function echoCodeCompiler(&$arrTheme)
    {
        $arrTheme['content'] = $this->encodeContent($this->phpTagStart() . 'echo ' . $arrTheme['content'] . ';' . $this->phpTagEnd());
    }

    /**
     * javascript 初始标签
     *
     * @param array $arrTheme
     * @return void
     */
    public function scriptCodeCompiler(&$arrTheme)
    {
        $arrTheme['content'] = $this->encodeContent('<script type="text/javascript">');
    }

    /**
     * css 初始标签
     *
     * @param array $arrTheme
     * @return void
     */
    public function styleCodeCompiler(&$arrTheme)
    {
        $arrTheme['content'] = $this->encodeContent('<style type="text/css">');
    }

    /**
     * endtag
     *
     * @param array $arrTheme
     * @return void
     */
    public function endtagCodeCompiler(&$arrTheme)
    {
        // 尾标签
        $calHelp = function ($sContent) {
            $sContent = ltrim(trim($sContent), '/');
            switch ($sContent) {
                case 'list':
                    $sContent = $this->phpTagStart() . 'endforeach; endif;' . $this->phpTagEnd();
                    break;
                case 'for':
                    $sContent = $this->phpTagStart() . 'endfor;' . $this->phpTagEnd();
                    break;
                case 'while':
                    $sContent = $this->phpTagStart() . 'endwhile;' . $this->phpTagEnd();
                    break;
                case 'if':
                    $sContent = $this->phpTagStart() . 'endif;' . $this->phpTagEnd();
                    break;
                case 'script':
                    $sContent = '</script>';
                    break;
                case 'style':
                    $sContent = '</style>';
                    break;
            }
            return $sContent;
        };

        $arrTheme['content'] = $calHelp(substr($arrTheme['source'], strpos($arrTheme['source'], '/'), strripos($arrTheme['source'], '}') - 1));
        $arrTheme['content'] = $this->encodeContent($arrTheme['content']);
    }

    /**
     * 变量及表达式
     *
     * @param array $arrTheme
     * @return void
     */
    public function jsvarCompiler(&$arrTheme)
    {
        $arrTheme['content'] = $this->phpTagStart() . 'echo ' . $this->parseJsContent($arrTheme['content']) . ';' . $this->phpTagEnd();
    }

    /**
     * let 编译器
     *
     * @param array $arrTheme
     * @return void
     */
    public function letJsCompiler(&$arrTheme)
    {
        $this->checkNode($arrTheme, true);
        $attr = $this->getNodeAttribute($arrTheme);
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

        $arrTheme['content'] = $this->phpTagStart() . "\${$name} = " . $value . ';' . $this->phpTagEnd();
    }

    /**
     * if 编译器
     *
     * @param array $arrTheme
     * @return void
     */
    public function ifJsCompiler(&$arrTheme)
    {
        $this->checkNode($arrTheme, true);
        $attr = $this->getNodeAttribute($arrTheme);
        $attr = $this->parseExpression(implode(' ', $attr));

        $arrTheme['content'] = $this->phpTagStart() . "if ({$attr}):" . $this->phpTagEnd();
        $arrTheme['content'] .= $this->getNodeBody($arrTheme);
        $arrTheme['content'] .= $this->phpTagStart() . "endif;" . $this->phpTagEnd();
    }

    /**
     * elseif 编译器
     *
     * @param array $arrTheme
     * @return void
     */
    public function elseifJsCompiler(&$arrTheme)
    {
        $this->checkNode($arrTheme, true);
        $attr = $this->getNodeAttribute($arrTheme);
        $attr = $this->parseExpression(implode(' ', $attr));

        $arrTheme['content'] = $this->phpTagStart() . "elseif ({$attr} ):" . $this->phpTagEnd();
        $arrTheme['content'] .= $this->getNodeBody($arrTheme);
    }

    /**
     * else
     *
     * @param array $arrTheme
     * @return void
     */
    public function elseJsCompiler(&$arrTheme)
    {
        $arrTheme['content'] = $this->phpTagStart() . "else:" . $this->phpTagEnd();
    }

    /**
     * for 循环
     *
     * @param array $arrTheme
     * @return void
     */
    public function forJsCompiler(&$arrTheme)
    {
        $this->checkNode($arrTheme, true);
        $attr = $this->getNodeAttribute($arrTheme);

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

        $arrTheme['content'] = $this->phpTagStart() . "foreach ({$attr} as \${$key} => \${$value}):" . $this->phpTagEnd();
        $arrTheme['content'] .= $this->getNodeBody($arrTheme);
        $arrTheme['content'] .= $this->phpTagStart() . "endforeach;" . $this->phpTagEnd();
    }

    /**
     * assign
     *
     * @param array $arrTheme
     * @return void
     */
    public function assignNodeCompiler(&$arrTheme)
    {
        $this->checkNode($arrTheme);
        $arrAttr = $this->getNodeAttribute($arrTheme);

        $arrAttr['name'] = $this->parseContent($arrAttr['name'], false);
        if ($arrAttr['value'] === null) {
            $arrAttr['value'] = '';
        } else {
            if ('$' == substr($arrAttr['value'], 0, 1)) {
                $arrAttr['value'] = $this->parseContent(substr($arrAttr['value'], 1));
            } else {
                $arrAttr['value'] = '\'' . $arrAttr['value'] . '\'';
            }
        }

        // 编译
        $arrTheme['content'] = $this->phpTagStart() . $arrAttr['name'] . '=' . $arrAttr['value'] . ';' . $this->phpTagEnd();
    }

    /**
     * if
     *
     * @param array $arrTheme
     * @return void
     */
    public function ifNodeCompiler(&$arrTheme)
    {
        $this->checkNode($arrTheme);
        $arrAttr = $this->getNodeAttribute($arrTheme);
        $arrAttr['condition'] = $this->parseConditionHelp($arrAttr['condition']);
        $arrTheme['content'] = $this->phpTagStart() . 'if (' . $arrAttr['condition'] . '):' . $this->phpTagEnd() . $this->getNodeBody($arrTheme) . $this->phpTagStart() . 'endif;' . $this->phpTagEnd();
    }

    /**
     * elseif
     *
     * @param array $arrTheme
     * @return void
     */
    public function elseifNodeCompiler(&$arrTheme)
    {
        $this->checkNode($arrTheme);
        $arrAttr = $this->getNodeAttribute($arrTheme);
        $arrAttr['condition'] = $this->parseConditionHelp($arrAttr['condition']);
        $arrTheme['content'] = $this->phpTagStart() . 'elseif ( ' . $arrAttr['condition'] . '):' . $this->phpTagEnd();
    }

    /**
     * else
     *
     * @param array $arrTheme
     * @return void
     */
    public function elseNodeCompiler(&$arrTheme)
    {
        $this->checkNode($arrTheme);
        $arrTheme['content'] = $this->phpTagStart() . 'else:' . $this->phpTagEnd();
    }

    /**
     * foreach list
     *
     * @param array $arrTheme
     * @return void
     */
    public function listNodeCompiler(&$arrTheme)
    {
        $this->checkNode($arrTheme);
        $arrAttr = $this->getNodeAttribute($arrTheme);

        foreach ([
            'key',
            'value',
            'index'
        ] as $sKey) {
            $arrAttr[$sKey] === null && $arrAttr[$sKey] = '$' . $sKey;
        }

        foreach ([
            'for',
            'key',
            'value',
            'index'
        ] as $sKey) {
            if ('$' . $sKey == $arrAttr[$sKey]) {
                continue;
            }
            $arrAttr[$sKey] = $this->parseContent($arrAttr[$sKey]);
        }

        // 编译
        $arrtheme['content'] = $this->phpTagStart() . $arrAttr['index'] . ' = 1;' . $this->phpTagEnd() . $this->phpTagStart() . 'if (is_array(' . $arrAttr['for'] . ')) : foreach(' . $arrAttr['for'] . ' as ' . $arrAttr['key'] . ' => ' . $arrAttr['value'] . '):' . $this->phpTagEnd() . $this->getNodeBody($arrTheme) . $this->phpTagStart() . $arrAttr['index'] . '++;' . $this->phpTagEnd() . $this->phpTagStart() . 'endforeach; endif;' . $this->phpTagEnd();
    }

    /**
     * lists 增强版
     *
     * @param array $arrTheme
     * @return void
     */
    public function listsNodeCompiler(&$arrTheme)
    {
        $this->checkNode($arrTheme);
        $arrAttr = $this->getNodeAttribute($arrTheme);

        $arrAttr['index'] === null && $arrAttr['index'] = 'index';
        $arrAttr['key'] === null && $arrAttr['key'] = 'key';
        $arrAttr['id'] === null && $arrAttr['id'] = 'id';
        $arrAttr['mod'] === null && $arrAttr['mod'] = 2;
        if (preg_match("/[^\d-.,]/", $arrAttr['mod'])) {
            $arrAttr['mod'] = '$' . $arrAttr['mod'];
        }
        $arrAttr['empty'] === null && $arrAttr['empty'] = '';
        $arrAttr['length'] === null && $arrAttr['length'] = '';
        $arrAttr['offset'] === null && $arrAttr['offset'] = '';
        $arrAttr['name'] = $this->parseContent($arrAttr['name']);

        $arrCompiled = [];
        $arrCompiled[] = $this->phpTagStart() . 'if (is_array(' . $arrAttr['name'] . ')): $' . $arrAttr['index'] . ' = 0;';
        if ('' != $arrAttr['length']) {
            $arrCompiled[] = '$arrList = array_slice(' . $arrAttr['name'] . ', ' . $arrAttr['offset'] . ', ' . $arrAttr['length'] . ');';
        } elseif ('' != $arrAttr['offset']) {
            $arrCompiled[] = '$arrList = array_slice(' . $arrAttr['name'] . ', ' . $arrAttr['offset'] . ');';
        } else {
            $arrCompiled[] = '$arrList = ' . $arrAttr['name'] . ';';
        }
        $arrCompiled[] = 'if (count( $arrList ) == 0): echo  "' . $arrAttr['empty'] . '";';
        $arrCompiled[] = 'else:';
        $arrCompiled[] = 'foreach($arrList as $' . $arrAttr['key'] . ' => $' . $arrAttr['id'] . '):';
        $arrCompiled[] = '++$' . $arrAttr['index'] . ';';
        $arrCompiled[] = '$mod = $' . $arrAttr['index'] . ' % ' . $arrAttr['mod'] . ';' . $this->phpTagEnd();
        $arrCompiled[] = $this->getNodeBody($arrTheme);
        $arrCompiled[] = $this->phpTagStart() . 'endforeach; endif; else: echo "' . $arrAttr['empty'] . '"; endif;' . $this->phpTagEnd();
        $arrTheme['content'] = implode(' ', $arrCompiled);
    }

    /**
     * include
     *
     * @param array $arrTheme
     * @return void
     */
    public function includeNodeCompiler(&$arrTheme)
    {
        $this->checkNode($arrTheme);
        $arrAttr = $this->getNodeAttribute($arrTheme);
        $arrAttr['file'] = str_replace('->', '.', $arrAttr['file']);

        // 后缀由主模板提供
        if (! $arrAttr['ext'] && strpos($arrAttr['file'], '.') !== false) {
            $temp = explode('.', $arrAttr['file']);
            $arrAttr['ext'] = '.' . array_pop($temp);
            $arrAttr['file'] = implode('.', $temp);
        }

        if (strpos($arrAttr['file'], '$') !== 0 && strpos($arrAttr['file'], '(') === false) {
            $arrAttr['file'] = (strpos($arrAttr['file'], '$') === 0 ? '' : '\'') . $arrAttr['file'] . '\'';
        }

        $arrTheme['content'] = $this->phpTagStart() . '$this->display(' . $arrAttr['file'] . ', [], \'' . ($arrAttr['ext'] ?  : '') . '\', true);' . $this->phpTagEnd();
    }

    /**
     * for
     *
     * @param array $arrTheme
     * @return void
     */
    public function forNodeCompiler(&$arrTheme)
    {
        $this->checkNode($arrTheme);
        $arrAttr = $this->getNodeAttribute($arrTheme);

        $arrAttr['step'] === null && $arrAttr['step'] = '1';
        $arrAttr['start'] === null && $arrAttr['start'] = '0';
        $arrAttr['end'] === null && $arrAttr['end'] = '0';
        $arrAttr['var'] === null && $arrAttr['var'] = 'var';
        $arrAttr['var'] = '$' . $arrAttr['var'];
        if ($arrAttr['type'] == '-') {
            $sComparison = ' >= ';
            $sMinusPlus = ' -= ';
        } else {
            $sComparison = ' <= ';
            $sMinusPlus = ' += ';
        }

        $arrCompiled = [];
        $arrCompiled[] = $this->phpTagStart() . 'for(' . $arrAttr['var'] . ' = ' . $arrAttr['start'] . ';';
        $arrCompiled[] = $arrAttr['var'] . $sComparison . $arrAttr['end'] . ';';
        $arrCompiled[] = $arrAttr['var'] . $sMinusPlus . $arrAttr['step'] . '):' . $this->phpTagEnd();
        $arrCompiled[] = $this->getNodeBody($arrTheme);
        $arrCompiled[] = $this->phpTagStart() . 'endfor;' . $this->phpTagEnd();

        $arrTheme['content'] = implode(' ', $arrCompiled);
    }

    /**
     * while
     *
     * @param array $arrTheme
     * @return void
     */
    public function whileNodeCompiler(&$arrTheme)
    {
        $this->checkNode($arrTheme);
        $arrAttr = $this->getNodeAttribute($arrTheme);

        $arrTheme['content'] = $this->phpTagStart() . 'while(' . $arrAttr['condition'] . '):' . $this->phpTagEnd() . $this->getNodeBody($arrTheme) . $this->phpTagStart() . 'endwhile;' . $this->phpTagEnd();
    }

    /**
     * break
     *
     * @param array $arrTheme
     * @return void
     */
    public function breakNodeCompiler(&$arrTheme)
    {
        $this->checkNode($arrTheme);
        $arrTheme['content'] = $this->phpTagStart() . 'break;' . $this->phpTagEnd();
    }

    /**
     * continue
     *
     * @param array $arrTheme
     * @return void
     */
    public function continueNodeCompiler(&$arrTheme)
    {
        $this->checkNode($arrTheme);
        $arrTheme['content'] = $this->phpTagStart() . 'continue;' . $this->phpTagEnd();
    }

    /**
     * php
     *
     * @param array $arrTheme
     * @return void
     */
    public function phpNodeCompiler(&$arrTheme)
    {
        $this->checkNode($arrTheme);
        $arrTheme['content'] = $this->phpTagStart() . $this->getNodeBody($arrTheme) . $this->phpTagEnd();
    }

    /**
     * 属性编译
     *
     * @param array $arrTheme
     * @return void
     */
    public function attributeNodeCompiler(&$arrTheme)
    {
        $sSource = trim($arrTheme['content']);
        $this->escapeRegexCharacter($sSource);

        if ($arrTheme['is_js'] === true) {
            $arrTag = $this->arrJsTag;
        } else {
            $arrTag = $this->arrNodeTag;
        }

        $arrAllowedAttr = $arrTag[$arrTheme['parent_name']]['attr'];

        // 正则匹配
        $arrRegexp = [];
        if (! $arrTheme['is_js']) {
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
                    } else {
                        // old:过滤掉不允许的属性
                        // new:js 风格的不能过滤
                        if (! in_array($sName, $arrAllowedAttr)) {
                           // continue;
                        }
                    }

                    $sValue = $arrRes[$nValueIdx][$nIdx];
                    $this->escapeRegexCharacter($sValue, false);
                    $arrTheme['attribute_list'][strtolower($sName)] = $sValue;
                }
            }
        }
        // 补全节点其余参数
        foreach ($arrAllowedAttr as $str) {
            if (! isset($arrTheme['attribute_list'][$str])) {
                $arrTheme['attribute_list'][$str] = null;
            }
        }
        $arrTheme['content'] = $sSource;
    }

    /**
     * 分析if
     *
     * @param string $sContent
     * @param string $sType
     * @return string
     */
    protected function parseContentIf($sContent, $sType = '')
    {
        $arrArray = explode(' ', $sContent);
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

        $sStr = str_replace(':', '->', $sStr);
        $sStr = str_replace('+', '::', $sStr);

        return $sType . "if({$sStr})";
    }

    /**
     * 解析 JS 变量内容
     *
     * @param string $sContent
     * @return string
     */
    protected function parseJsContent($content)
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
        for($i=0;$i<strlen($content);$i++) {
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
     * @param string $sContent
     * @param bool $booFunc 是否允许解析函数
     * @return string
     */
    protected function parseContent($sContent, $booFunc = true)
    {
        $sContent = str_replace(':', '->', $sContent); // 以|分割字符串,数组第一位是变量名字符串,之后的都是函数参数&&函数{$hello|md5}

        $arrVar = explode('|', $sContent);
        $sVar = array_shift($arrVar); // 弹出第一个元素,也就是变量名
        if (strpos($sVar, '.')) { // 访问数组元素或者属性
            $arrVars = explode('.', $sVar);
            if (substr($arrVars['1'], 0, 1) == "'" or substr($arrVars['1'], 0, 1) == '"' or substr($arrVars['1'], 0, 1) == "$") {
                $sName = '$' . $arrVars[0] . '.' . $arrVars[1] . ($this->arrayHandler($arrVars, 3)); // 特殊的.连接字符串
            } else {
                $sName = '$' . $arrVars[0] . '->' . $arrVars[1] . ($this->arrayHandler($arrVars, 2));
            }
            $sVar = $arrVars[0];
        } elseif (strpos($sVar, '[')) { // $hello['demo'] 方式访问数组
            $sName = "$" . $sVar;
            preg_match('/(.+?)\[(.+?)\]/is', $sVar, $arrArray);
            $sVar = $arrArray[1];
        } else {
            $sName = "\$$sVar";
        }

        if ($booFunc === true && count($arrVar) > 0) { // 如果有使用函数
            $sName = $this->parseVarFunction($sName, $arrVar); // 传入变量名,和函数参数继续解析,这里的变量名是上面的判断设置的值
        }

        $sName = str_replace('^', ':', $sName);
        return ! empty($sName) ? $sName : '';
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
                $arrArgs[0] = str_replace('+', '::', $arrArgs[0]);
                if (isset($arrArgs[1])) {
                    $arrArgs[1] = str_replace('->', ':', $arrArgs[1]);
                }
            }

            switch (strtolower($arrArgs[0])) {
                case 'default': // 特殊模板函数
                    $sName = $sName . ' ?  : ' . $arrArgs[1];
                    break;
                default: // 通用模板函数
                    if (isset($arrArgs[1])) {
                        if (strstr($arrArgs[1], '**')) {
                            $arrArgs[1] = str_replace('**', $sName, $arrArgs[1]);
                            $sName = "$arrArgs[0] ( $arrArgs[1] )";
                        } else {
                            $sName = "$arrArgs[0] ( $sName, $arrArgs[1] )";
                        }
                    } elseif (! empty($arrArgs[0])) {
                        $sName = "$arrArgs[0] ( $sName )";
                    }
            }
        }

        return $sName;
    }

    /**
     * 转换对象方法和静态方法连接符
     *
     * @param string $sContent
     * @return string
     */
    protected function parseConditionHelp($sContent)
    {
        return str_replace([
            ':',
            '+'
        ], [
            '->',
            '::'
        ], $sContent);
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
        if ($nType == 1) { // 类似$hello['test']['test2']
            for ($nI = $nGo; $nI < $nLen; $nI ++) {
                $sParam .= "['{$arrVars[$nI]}']";
            }
        } elseif ($nType == '2') { // 类似$hello->test1->test2
            for ($nI = $nGo; $nI < $nLen; $nI ++) {
                $sParam .= "->{$arrVars[$nI]}";
            }
        } elseif ($nType == '3') { // 类似$hello.test1.test2
            for ($nI = $nGo; $nI < $nLen; $nI ++) {
                $sParam .= ".{$arrVars[$nI]}";
            }
        }

        return $sParam;
    }

    /**
     * 编码内容
     *
     * @param string $sContent
     * @param string $sContent
     * @return string
     */
    protected function encodeContent($sContent, $sType = '')
    {
        if ($sType == 'global') {
            $sContent = parser::globalEncode($sContent);
        } elseif (in_array($sType, [
            'revert',
            'include'
        ])) {
            $sContent = base64_decode($sContent);
        } else {
            $sContent = parser::revertEncode($sContent);
        }
        return $sContent;
    }

    /**
     * 验证节点是否正确
     *
     * @param array $arrTheme
     * @return boolean
     */
    protected function checkNode($arrTheme, $bJsNode = false)
    {
        $arrAttribute = $arrTheme['children'][0];

        // 验证标签的属性值
        if ($arrAttribute['is_attribute'] !== true) {
            throw new InvalidArgumentException('Tag attribute type validation failed.');
        }

        // 验证必要属性
        $arrTag = $bJsNode === true ? $this->arrJsTag : $this->arrNodeTag;
        if (! isset($arrTag[$arrTheme['name']])) {
            throw new InvalidArgumentException(sprintf('The tag %s is undefined.', $arrTheme['name']));
        }

        foreach ($arrTag[$arrTheme['name']]['required'] as $sName) {
            $sName = strtolower($sName);
            if (! isset($arrAttribute['attribute_list'][$sName])) {
                throw new InvalidArgumentException(sprintf('The node %s lacks the required property: %s.', $arrTheme['name'], $sName));
            }
        }

        return true;
    }

    /**
     * 取得节点的属性列表
     *
     * @param array $arrTheme 节点
     * @return array
     */
    protected function getNodeAttribute($arrTheme)
    {
        foreach ($arrTheme['children'] as $arrChild) {
            if (isset($arrChild['is_attribute']) && $arrChild['is_attribute'] == 1) {
                return $arrChild['attribute_list'];
            }
        }
        return [];
    }

    /**
     * 取得body编译内容
     *
     * @param array $arrTheme 节点
     * @return array
     */
    protected function getNodeBody($arrTheme)
    {
        foreach ($arrTheme['children'] as $arrChild) {
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

        if ($bEsc) { // 转义
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
        } else { // 还原
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
        return ' ?' . '>';
    }
}
