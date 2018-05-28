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
use Leevel\Stack\Stack;

/**
 * 分析模板
 * This class borrows heavily from the JeCat Framework and is part of the JeCat package
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2016.11.19
 * @version 1.0
 * @link http://jecat.cn
 */
class Parser implements IParser
{

    /**
     * 编译器
     *
     * @var \Leevel\View\ICompiler
     */
    protected $objCompiler;

    /**
     * 成对节点栈
     *
     * @var stack
     */
    protected $oNodeStack;

    /**
     * js 风格 和 node 共用分析器
     *
     * @var boolean
     */
    protected $bJsNode = false;

    /**
     * 编译器
     *
     * @var array
     */
    protected $arrCompilers = [];

    /**
     * 分析器
     *
     * @var array
     */
    protected $arrParses = [];

    /**
     * 分析器定界符
     *
     * @var array
     */
    protected $arrTag = [

        // 全局
        'global' => [
            'left' => '[<\{]',
            'right' => '[\}>]'
        ],

        // js 风格代码
        'js' => [
            'left' => '{%',
            'right' => '%}'
        ],

        // js 风格变量代码
        'jsvar' => [
            'left' => '{{',
            'right' => '}}'
        ],

        // 代码
        'code' => [
            'left' => '{',
            'right' => '}'
        ],

        // 节点
        'node' => [
            'left' => '<',
            'right' => '>'
        ],

        // 反向
        'revert' => [],

        // 全局反向
        'globalrevert' => []
    ];

    /**
     * 模板树结构
     *
     * @var array
     */
    protected $arrThemeTree = [];

    /**
     * 模板项结构
     *
     * @var array
     */
    protected $arrThemeStruct = [
        'source' => '',  // 原模板
        'content' => '',  //
        'compiler' => null,  // 编译器
        'children' => [],
        'position' => []
    ];

    /**
     * 当前编译源文件
     *
     * @var string
     */
    protected $strSourceFile;

    /**
     * 当前编译缓存文件
     *
     * @var array
     */
    protected $stringCachePath;

    /**
     * 构造函数
     *
     * @param \Leevel\View\ICompiler $objCompiler
     * @return void
     */
    public function __construct(icompiler $objCompiler)
    {
        $this->objCompiler = $objCompiler;
    }

    /**
     * 注册视图编译器
     *
     * @return $this
     */
    public function registerCompilers()
    {
        foreach ($this->objCompiler->getCompilers() as $arrCompiler) {
            $this->registerCompiler($arrCompiler[0], $arrCompiler[1], $arrCompiler[2]);
        }
        return $this;
    }

    /**
     * 注册视图分析器
     *
     * @return $this
     */
    public function registerParsers()
    {
        foreach ($this->arrTag as $sKey => $arr) {
            $this->registerParser($sKey);
        }
        return $this;
    }

    /**
     * 执行编译
     *
     * @param string $sFile
     * @param string $sCachePath
     * @param boolean $bReturn
     * @return string
     */
    public function doCombile($sFile, $sCachePath, $bReturn = false)
    {
        if (! is_file($sFile)) {
            throw new InvalidArgumentException(printf('file %s is not exits', $sFile));
        }

        $this->strSourceFile = $sFile;
        $this->strCachePath = $sCachePath;

        // 源码
        $sCache = file_get_contents($sFile);

        // 逐个载入分析器编译模板
        foreach ($this->arrParses as $sParser) {
            // 清理对象 & 构建顶层树对象
            $this->clearThemeTree();
            $arrTheme = [
                'source' => $sCache,
                'content' => $sCache,
                'position' => $this->getPosition($sCache, '', 0)
            ];

            $arrTheme = array_merge($this->getDefaultStruct(), $arrTheme);
            $this->topTheme($arrTheme);

            // 分析模板生成模板树
            $sParser = $sParser . 'Parse';
            $this->{$sParser}($sCache); // 分析

            // 编译模板树
            $sCache = $this->compileThemeTree();
        }

        // 生成编译文件
        if ($bReturn === false) {
            $this->makeCacheFile($sCachePath, $sCache);
        } else {
            return $sCompiled;
        }
    }

    /**
     * code 编译编码，后还原
     *
     * @param string $sContent
     * @return string
     */
    public static function revertEncode($sContent)
    {
        $nRand = rand(1000000, 9999999);
        return "__##revert##START##{$nRand}@" . base64_encode($sContent) . '##END##revert##__';
    }

    /**
     * tagself 编译编码，后还原
     *
     * @param string $sContent
     * @return string
     */
    public static function globalEncode($sContent)
    {
        $nRand = rand(1000000, 9999999);
        return "__##global##START##{$nRand}@" . base64_encode($sContent) . '##END##global##__';
    }

    /**
     * 全局编译器 tagself
     *
     * @param string $sCompiled
     * @return void
     */
    protected function globalParse(&$sCompiled)
    {
        $arrTag = $this->getTag('global');

        $arrRes = []; // 分析
        if (preg_match_all("/{$arrTag['left']}tagself{$arrTag['right']}(.+?){$arrTag['left']}\/tagself{$arrTag['right']}/isx", $sCompiled, $arrRes)) {
            $nStartPos = 0;
            foreach ($arrRes[1] as $nIndex => $sEncode) {
                $sSource = trim($arrRes[0][$nIndex]);
                $sContent = trim($arrRes[1][$nIndex]);

                $arrTheme = [
                    'source' => $sSource,
                    'content' => $sContent,
                    'compiler' => 'global',  // 编译器
                    'children' => []
                ];

                $arrTheme['position'] = $this->getPosition($sCompiled, $sSource, $nStartPos);
                $nStartPos = $arrTheme['position']['end'] + 1;
                $arrTheme = array_merge($this->arrThemeStruct, $arrTheme);
                $this->addTheme($arrTheme); // 将模板数据加入到树结构中
            }
        }
    }

    /**
     * js 风格 变量分析器
     *
     * @param string $sCompiled
     * @return void
     */
    protected function jsvarParse(&$sCompiled)
    {
        $arrTag = $this->getTag('jsvar');

        $arrRes = []; // 分析
        if (preg_match_all("/{$arrTag['left']}(.+?){$arrTag['right']}/isx", $sCompiled, $arrRes)) {
            $nStartPos = 0;
            foreach ($arrRes[1] as $nIndex => $sEncode) {
                $sSource = trim($arrRes[0][$nIndex]);
                $sContent = trim($arrRes[1][$nIndex]);

                $arrTheme = [
                    'source' => $sSource,
                    'content' => $sContent,
                    'compiler' => 'jsvar',  // 编译器
                    'children' => []
                ];

                $arrTheme['position'] = $this->getPosition($sCompiled, $sSource, $nStartPos);
                $nStartPos = $arrTheme['position']['end'] + 1;
                $arrTheme = array_merge($this->arrThemeStruct, $arrTheme);
                $this->addTheme($arrTheme); // 将模板数据加入到树结构中
            }
        }
    }

    /**
     * code 方式分析器
     *
     * @param string $sCompiled
     * @return void
     */
    protected function codeParse(&$sCompiled)
    {
        foreach ($this->arrCompilers['code'] as $sCompilers => $sTag) {
            // 处理一些正则表达式中有特殊意义的符号
            $arrNames[] = $this->escapeRegexCharacter($sCompilers); 
        }

        // 没有任何编译器
        if (! count($arrNames)) { 
            return;
        }

        // 正则分析
        $arrTag = $this->getTag('code');
        $sNames = implode('|', $arrNames);
        $sRegexp = "/" . $arrTag['left'] . "\s*({$sNames})(|.+?)" . $arrTag['right'] . "/s";

        // 分析
        $arrRes = []; 
        if (preg_match_all($sRegexp, $sCompiled, $arrRes)) {
            $nStartPos = 0;
            foreach ($arrRes[0] as $nIdx => &$sSource) {
                $sObjType = trim($arrRes[1][$nIdx]);
                ! $sObjType && $sObjType = '/';
                $sContent = trim($arrRes[2][$nIdx]);

                $arrTheme = [
                    'source' => $sSource,
                    'content' => $sContent,
                    'compiler' => $this->arrCompilers['code'][$sObjType] . 'Code',  // 编译器
                    'children' => []
                ];
                $arrTheme['position'] = $this->getPosition($sCompiled, $sSource, $nStartPos);
                $nStartPos = $arrTheme['position']['end'] + 1;
                $arrTheme = array_merge($this->arrThemeStruct, $arrTheme);
                $this->addTheme($arrTheme); // 将模板数据加入到树结构中
            }
        }
    }

    /**
     * js 风格分析器 与 node 公用分析器
     *
     * @param string $sCompiled
     * @return void
     */
    protected function jsParse(&$sCompiled)
    {
        $this->bJsNode = true;

        // 查找分析 Node 的标签
        $this->findNodeTag($sCompiled);

        // 用标签组装 Node
        $this->packNode($sCompiled); 
    }

    /**
     * node 分析器
     *
     * @param string $sCompiled
     * @return void
     */
    protected function nodeParse(&$sCompiled)
    {
        $this->bJsNode = false;

        // 查找分析 Node 的标签
        $this->findNodeTag($sCompiled); 

        // 用标签组装 Node
        $this->packNode($sCompiled); 
    }

    /**
     * code 还原分析器
     *
     * @param string $sCompiled
     * @return void
     */
    protected function revertParse(&$sCompiled)
    {
        // 分析
        $arrRes = []; 
        if (preg_match_all('/__##revert##START##\d+@(.+?)##END##revert##__/', $sCompiled, $arrRes)) {
            $nStartPos = 0;
            foreach ($arrRes[1] as $nIndex => $sEncode) {
                $sSource = $arrRes[0][$nIndex];

                $arrTheme = [
                    'source' => $sSource,
                    'content' => $sEncode,

                    // 编译器
                    'compiler' => 'revert',  
                    'children' => []
                ];

                $arrTheme['position'] = $this->getPosition($sCompiled, $sSource, $nStartPos);
                $nStartPos = $arrTheme['position']['end'] + 1;
                $arrTheme = array_merge($this->arrThemeStruct, $arrTheme);

                // 将模板数据加入到树结构中
                $this->addTheme($arrTheme); 
            }
        }
    }

    /**
     * tagself 还原分析器
     *
     * @param string $sCompiled
     * @return void
     */
    protected function globalrevertParse(&$sCompiled)
    {
        // 分析
        $arrRes = []; 
        if (preg_match_all('/__##global##START##\d+@(.+?)##END##global##__/', $sCompiled, $arrRes)) {
            $nStartPos = 0;

            foreach ($arrRes[1] as $nIndex => $sEncode) {
                $sSource = $arrRes[0][$nIndex];
                $sContent = $arrRes[1][$nIndex];

                $arrTheme = [
                    'source' => $sSource,
                    'content' => $sContent,

                    // 编译器
                    'compiler' => 'globalrevert',  
                    'children' => []
                ];

                $arrTheme['position'] = $this->getPosition($sCompiled, $sSource, $nStartPos);
                $nStartPos = $arrTheme['position']['end'] + 1;
                $arrTheme = array_merge($this->arrThemeStruct, $arrTheme);

                // 将模板数据加入到树结构中
                $this->addTheme($arrTheme); 
            }
        }
    }

    /**
     * 查找成对节点
     *
     * @param string $sCompiled
     * @return void
     */
    protected function findNodeTag(&$sCompiled)
    {
        // 设置一个栈
        $this->oNodeStack = new Stack(['array']);

        // 判断是那种编译器
        $sNodeType = $this->bJsNode === true ? 'js' : 'node';

        // 所有一级节点名称
        foreach ($this->arrCompilers[$sNodeType] as $sCompilers => $sTag) { 

            // 处理一些正则表达式中有特殊意义的符号
            $arrNames[] = $this->escapeRegexCharacter($sCompilers); 
        }

        // 没有任何编译器
        if (! count($arrNames)) { 
            return;
        }
        // 正则分析
        $arrTag = $this->getTag($sNodeType);
        $sNames = implode('|', $arrNames);
        $sRegexp = "/{$arrTag['left']}\s*(\/?)(({$sNames})(:[^\s" . ($this->bJsNode === true ? '' : "\>") . "\}]+)?)(\s[^" . ($this->bJsNode === true ? '' : ">") . "\}]*?)?\/?{$arrTag['right']}/isx";

        // 标签名称位置
        $nNodeNameIdx = 2; 

        // 标签顶级名称位置
        $nNodeTopNameIdx = 3; 

        // 尾标签斜线位置
        $nTailSlasheIdx = 1; 

        // 标签属性位置
        $nTagAttributeIdx = 5; 

        if ($this->bJsNode === true) {
            $arrCompiler = $this->arrCompilers['js'];
        } else {
            $arrCompiler = $this->arrCompilers['node'];
        }

        // 依次创建标签对象
        if (preg_match_all($sRegexp, $sCompiled, $arrRes)) { 
            $nStartPos = 0;
            foreach ($arrRes[0] as $nIdx => &$sTagSource) {
                $sNodeName = $arrRes[$nNodeNameIdx][$nIdx];
                $sNodeTopName = $arrRes[$nNodeTopNameIdx][$nIdx];
                $nNodeType = $arrRes[$nTailSlasheIdx][$nIdx] === '/' ? 'tail' : 'head';

                // 将节点名称统一为小写
                $sNodeName = strtolower($sNodeName); 
                $sNodeTopName = strtolower($sNodeTopName);

                $arrTheme = [
                    'source' => $sTagSource,
                    'name' => $arrCompiler[$sNodeTopName],
                    'type' => $nNodeType
                ];

                // 头标签的属性
                if ($nNodeType == 'head') {
                    $arrTheme['attribute'] = $arrRes[$nTagAttributeIdx][$nIdx];
                } else {
                    $arrTheme['attribute'] = '';
                }
                $arrTheme['content'] = $arrTheme['attribute'];
                $arrTheme['position'] = $this->getPosition($sCompiled, $sTagSource, $nStartPos);
                $nStartPos = $arrTheme['position']['end'] + 1;
                $arrTheme = array_merge($this->arrThemeStruct, $arrTheme);
                $this->oNodeStack->in($arrTheme); // 加入到标签栈
            }
        }
    }

    /**
     * 装配节点
     *
     * @param string $sCompiled
     * @return void
     */
    protected function packNode(&$sCompiled)
    {
        if ($this->bJsNode === true) {
            $arrNodeTag = $this->objCompiler->getJsTagHelp();
            $sCompiler = 'Js';
        } else {
            $arrNodeTag = $this->objCompiler->getNodeTagHelp();
            $sCompiler = 'Node';
        }

        // 尾标签栈
        $oTailStack = new Stack(['array']);

        // 载入节点属性分析器 & 依次处理所有标签
        while (($arrTag = $this->oNodeStack->out()) !== null) {
            
            // 尾标签，加入到尾标签中
            if ($arrTag['type'] == 'tail') {
                $oTailStack->in($arrTag);
                continue;
            }

            // 从尾标签栈取出一项
            $arrTailTag = $oTailStack->out(); 

            // 单标签节点
            if (! $arrTailTag or ! $this->findHeadTag($arrTag, $arrTailTag)) { 
                if ($arrNodeTag[$arrTag['name']]['single'] !== true) {
                    throw new InvalidArgumentException(sprintf('%s type nodes must be used in pairs, and no corresponding tail tags are found.', $arrTag['name']) . '<br />' . $this->getLocation($arrTag['position']));
                }

                // 退回栈中
                if ($arrTailTag) { 
                    $oTailStack->in($arrTailTag);
                }

                $arrThemeNode = [
                    'content' => $arrTag['content'],

                    // 编译器
                    'compiler' => $arrTag['name'] . $sCompiler,  
                    'source' => $arrTag['source'],
                    'name' => $arrTag['name']
                ];
                $arrThemeNode['position'] = $arrTag['position'];
                $arrThemeNode = array_merge($this->arrThemeStruct, $arrThemeNode);
            }

            // 成对标签
            else {

                // 头尾标签中间为整个标签内容
                $nStart = $arrTag['position']['start'];
                $nLen = $arrTailTag['position']['end'] - $nStart + 1;
                $sSource = substr($sCompiled, $nStart, $nLen);

                $arrThemeNode = [
                    'content' => $sSource,

                    // 编译器
                    'compiler' => $arrTag['name'] . $sCompiler,  
                    'source' => $sSource,
                    'name' => $arrTag['name']
                ];
                $arrThemeNode['position'] = $this->getPosition($sCompiled, $sSource, $nStart);
                $arrThemeNode = array_merge($this->arrThemeStruct, $arrThemeNode);

                // 标签body
                $nStart = $arrTag['position']['end'] + 1;
                $nLen = $arrTailTag['position']['start'] - $nStart;
                if ($nLen > 0) {
                    $sBody = substr($sCompiled, $nStart, $nLen);
                    $arrThemeBody = [
                        'content' => $sBody,

                        // 编译器
                        'compiler' => null,  
                        'source' => $sBody,
                        'is_body' => 1
                    ];
                    $arrThemeBody['position'] = $this->getPosition($sCompiled, $sBody, $nStart);
                    $arrThemeBody = array_merge($this->arrThemeStruct, $arrThemeBody);
                    $arrThemeNode = $this->addThemeTree($arrThemeNode, $arrThemeBody);
                }
            }

            // 标签属性
            $arrThemeAttr = [
                'content' => $arrTag['content'],

                // 编译器
                'compiler' => 'attributeNode',  
                'source' => $arrTag['source'],
                'attribute_list' => [],
                'is_attribute' => true,
                'parent_name' => $arrThemeNode['name'],
                'is_js' => $this->bJsNode
            ];

            $arrThemeAttr['position'] = $this->getPosition($sCompiled, $arrTag['source'], 0);
            $arrThemeAttr = array_merge($this->arrThemeStruct, $arrThemeAttr);
            $arrThemeNode = $this->addThemeTree($arrThemeNode, $arrThemeAttr);

            // 将模板数据加入到树结构中
            $this->addTheme($arrThemeNode); 
        }
    }

    /**
     * 查找 node 标签
     *
     * @param array $arrTag
     * @param array $arrTailTag
     * @return boolean
     */
    protected function findHeadTag($arrTag, $arrTailTag)
    {
        if ($arrTailTag['type'] != 'tail') {
            throw new InvalidArgumentException(sprintf('The parameter must be a tail tag.'));
        }
        return preg_match("/^{$arrTailTag['name']}/i", $arrTag['name']);
    }

    /**
     * 注册分析器
     *
     * @param string $sTag
     * @return void
     */
    protected function registerParser($sTag)
    {
        $this->arrParses[] = $sTag;
    }

    /**
     * 注册编译器 code和node编译器注册
     *
     * @param string $sType code 代码标签 node 节点标签
     * @param string|array $mixName ~ 标签 : 标签 while 标签
     * @param array|string $Tag 标签对应的编译器
     * @return void
     */
    protected function registerCompiler($sType, $mixName, $sTag)
    {
        if (! is_array($mixName)) {
            $mixName = (array)$mixName;
        }
        foreach ($mixName as $sTemp) {
            $this->arrCompilers[$sType][$sTemp] = $sTag;
        }
    }

    /**
     * 逐个编译模板树
     *
     * @return string
     */
    protected function compileThemeTree()
    {
        // 逐个编译
        $sCache = '';
        foreach ($this->arrThemeTree as $arrTheme) {
            $this->compileTheme($arrTheme);
            $sCache .= $arrTheme['content'];
        }
        return $sCache;
    }

    /**
     * 分析模板调用编译器编译
     *
     * @param array $arrTheme 待编译的模板
     * @return void
     */
    protected function compileTheme(&$arrTheme)
    {
        foreach ($arrTheme['children'] as $intKey => $arrOne) {
            $strSource = $arrOne['source'];

            // 编译子对象
            $this->compileTheme($arrOne); 
            $arrTheme['children'][$intKey] = $arrOne;

            // 置换对象
            $nStart = strpos($arrTheme['content'], $strSource);
            $nLen = $arrOne['position']['end'] - $arrOne['position']['start'] + 1;
            $arrTheme['content'] = substr_replace($arrTheme['content'], $arrOne['content'], $nStart, $nLen);
        }

        // 编译自身
        if ($arrTheme['compiler']) {
            $strCompilers = $arrTheme['compiler'] . 'Compiler';
            $this->objCompiler->{$strCompilers}($arrTheme);
        }
    }

    /**
     * 创建缓存文件
     *
     * @param string $sCachePath
     * @param string $sCompiled
     * @return void
     */
    protected function makeCacheFile($sCachePath, &$sCompiled)
    {
        ! is_file($sCachePath) && ! is_dir(dirname($sCachePath)) && mkdir(dirname($sCachePath), 0777, true);

        file_put_contents($sCachePath, '<?' . 'php /* ' . date('Y-m-d H:i:s') . ' */ ?' . '>' . PHP_EOL . $sCompiled);

        chmod($sCachePath, 0777);
    }

    /**
     * 取得模板分析器定界符
     *
     * @param string $sType
     * @return array
     */
    protected function getTag($sType)
    {
        return $this->arrTag[$sType];
    }

    /**
     * 将模板结构加入树结构中去
     *
     * @param array $arrTheme
     * @return void
     */
    protected function addTheme($arrTheme)
    {
        $arrTop = &$this->arrThemeTree[0];
        $arrTop = $this->addThemeTree($arrTop, $arrTheme);
    }

    /**
     * 清理模板树对象
     *
     * @return void
     */
    protected function clearThemeTree()
    {
        $this->arrThemeTree = [];
    }

    /**
     * 添加顶层树对象
     *
     * @param array $arrTheme
     * @return void
     */
    protected function topTheme($arrTheme)
    {
        $this->arrThemeTree[] = $arrTheme;
    }

    /**
     * 将新的模板加入到树结构中去
     *
     * @param array $arrTop 顶层模板
     * @param array $arrNew 待加入的模板
     * @return array
     */
    protected function addThemeTree($arrTop, $arrNew)
    {
        $arrResult = [];

        foreach ($arrTop['children'] as $arrChild) {
            if ($arrNew) {
                $sRelative = $this->positionRelative($arrNew['position'], $arrChild['position']);

                switch ($sRelative) {

                    /**
                     * 新增的和上次处于平级关系直接加入上级的 children 容器中
                     * new 在前 child 在后面
                     */
                    case 'front':
                        $arrResult[] = $arrNew;
                        $arrResult[] = $arrChild;
                        $arrNew = null;
                        break;

                    /**
                     * 新增的和上次处于平级关系直接加入上级的 children 容器中
                     * child 在前 new 在后面
                     */
                    case 'behind':
                        $arrResult[] = $arrChild;
                        break;

                    /**
                     * new 处于 child 内部
                     * new 在 child 内部
                     */
                    case 'in':
                        $arrChild = $this->addThemeTree($arrChild, $arrNew);
                        $arrResult[] = $arrChild;
                        $arrNew = null;
                        break;

                    /**
                     * child 处于 new 内部
                     * child 在 new 内部
                     */
                    case 'out':
                        $arrNew = $this->addThemeTree($arrNew, $arrChild);
                        break;
                }
            } else {
                $arrResult[] = $arrChild;
            }
        }

        if ($arrNew) {
            $arrResult[] = $arrNew;
        }

        $arrTop['children'] = $arrResult;
        return $arrTop;
    }

    /**
     * 分析匹配标签的位置
     *
     * @param string $sContent 待编译的模板
     * @param string $sFind 匹配的标签
     * @param int $nStart 起始查找的位置
     * @return array start 标签开始的位置（字节数）
     * @note int end 标签结束的位置（字节数）
     * @notenote int start_line 标签开始的行（行数）
     * @note int end_line 标签结束的行（行数）
     * @note int start_in 标签开始的所在的行的起始字节数
     * @note int end_in 标签结束的所在的行的起始字节数
     */
    protected function getPosition($sContent, $sFind, $nStart)
    {
        /*
         *
         * ======= start =======
         *
         * {tagself}
         * yes
         * {/tagself}
         *
         * ======== end =======
         *
         * Array
         * (
         * [start] => 27
         * [end] => 64
         * [start_line] => 2
         * [end_line] => 4
         * [start_in] => 2
         * [end_in] => 17
         * )
         */
        $arrData = [];

        if (empty($sFind)) { // 空
            $arrData['start'] = - 1;
            $arrData['end'] = - 1;
            $arrData['start_line'] = - 1;
            $arrData['end_line'] = - 1;
            $arrData['start_in'] = - 1;
            $arrData['end_in'] = - 1;
            return $arrData;
        }

        $nTotal = strlen($sContent);

        // 起止字节位置
        $nStart = strpos($sContent, $sFind, $nStart);
        $nEnd = $nStart + strlen($sFind) - 1;

        // 起止行数
        $nStartLine = $nStart <= 0 ? 0 : substr_count($sContent, "\n", 0, $nStart);
        $nEndLine = $nEnd <= 0 ? 0 : substr_count($sContent, "\n", 0, $nEnd);

        /**
         * 匹配模块范围圈（在这个字节里面的都是它的子模快）
         * 找到开始和结束的地方就确定了这个模块所在区域范围
         */

        // 起点的行首换行位置 && 结束点的行首位置
        $nLineStartFirst = strrpos(substr($sContent, 0, $nStart), "\n") + 1;
        $nLineEndFirst = strrpos(substr($sContent, 0, $nEnd), "\n") + 1;
        $nStartIn = $nStart - $nLineStartFirst;
        $nEndIn = $nEnd - $nLineEndFirst;

        /**
         * 返回结果
         */
        $arrData['start'] = $nStart;
        $arrData['end'] = $nEnd;
        $arrData['start_line'] = $nStartLine;
        $arrData['end_line'] = $nEndLine;
        $arrData['start_in'] = $nStartIn;
        $arrData['end_in'] = $nEndIn;

        return $arrData;
    }

    /**
     * 对比两个模板相对位置
     * 这个和两个时间段之间的关系一样，其中交叉在模板引擎中是不被支持，因为无法实现
     * 除掉交叉，剩下包含、被包含、前面和后面，通过位置组装成一颗树结构
     *
     * @param array $arrOne 待分析的第一个模板
     * @param array $arrTwo 待分析的第二个模板
     * @return string
     * @note string front 第一个在第二个前面
     * @note string behind 第一个在第二个后面
     * @note string in 第一个在第二里面，成为它的子模板
     * @note string out 第一个在第一个里面，成为它的子模板
     */
    protected function positionRelative($arrOne, $arrTwo)
    {

        /**
         * 第一个匹配的标签在第二个前面
         * 条件：第一个结束字节位置 <= 第二个开始位置
         */
        /*
         * ======= start =======
         *
         * {if}
         * one
         * {/if}
         *
         * {for}
         * two
         * {/for}
         *
         * ======== end =======
         */
        if ($arrOne['end'] <= $arrTwo['start']) {
            return 'front';
        }

        /**
         * 第一个匹配的标签在第二个后面
         * 条件：第一个开始字节位置 >= 第二个结束位置
         */

        /*
         * ======= start =======
         *
         * {for}
         * two
         * {/for}
         *
         * {if}
         * one
         * {/if}
         *
         * ======== end =======
         */
        if ($arrOne['start'] >= $arrTwo['end']) {
            return 'behind';
        }

        /**
         * 第一个匹配的标签在第二个里面
         * 条件：第一个开始字节位置 >= 第二个开始位置
         */

        /*
         * ======= start =======
         *
         * {for}
         * two
         *
         * {if}
         * one
         * {/if}
         *
         * {/for}
         *
         * ======== end =======
         */
        if ($arrOne['start'] >= $arrTwo['start']) {
            return 'in';
        }

        /**
         * 第一个匹配的标签在第二个外面
         * 条件：第一个开始字节位置 <= 第二个开始位置
         */

        /*
         * ======= start =======
         *
         * {if}
         * one
         *
         * {for}
         * two
         * {/for}
         *
         * {/if}
         *
         * ======== end =======
         */
        if ($arrOne['start'] <= $arrTwo['start']) {
            return 'out';
        }

        /**
         * 交叉（两个时间段相互关系）
         */
        throw new InvalidArgumentException('Template engine tag library does not support cross.');
    }

    /**
     * 取得默认模板项结构
     *
     * @return array
     */
    protected function getDefaultStruct()
    {
        return $this->arrThemeStruct;
    }

    /**
     * 转义正则表达式特殊字符
     *
     * @param string $sTxt
     * @return string
     */
    protected function escapeRegexCharacter($sTxt)
    {
        $sTxt = str_replace([
            '$',
            '/',
            '?',
            '*',
            '.',
            '!',
            '-',
            '+',
            '(',
            ')',
            '[',
            ']',
            ',',
            '{',
            '}',
            '|'
        ], [
            '\$',
            '\/',
            '\\?',
            '\\*',
            '\\.',
            '\\!',
            '\\-',
            '\\+',
            '\\(',
            '\\)',
            '\\[',
            '\\]',
            '\\,',
            '\\{',
            '\\}',
            '\\|'
        ], $sTxt);

        return $sTxt;
    }

    /**
     * 取得模板位置
     *
     * @param array $arrPosition
     * @return string
     */
    protected function getLocation($arrPosition)
    {
        return sprintf('Line:%s; column:%s; file:%s.', $arrPosition['start_line'], $arrPosition['start_in'], $this->strSourceFile) . $this->getLocationSource($arrPosition);
    }

    /**
     * 取得模板位置源码
     *
     * @param array $arrPosition
     * @return string
     */
    protected function getLocationSource($arrPosition)
    {
        $arrLine = explode(PHP_EOL, htmlentities(substr(file_get_contents($this->strSourceFile), $arrPosition['start'], $arrPosition['end'])));
        $arrLine[] = '<div class="key">' . array_pop($arrLine) . '</div>';
        return '<pre><code>' . implode(PHP_EOL, $arrLine) . '</code></pre>';
    }
}
