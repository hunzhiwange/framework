<?php
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
namespace Queryyetsimple\Bootstrap\Console;

use RuntimeException;
use Queryyetsimple\Filesystem\Fso;

/**
 * 命令行工具类导入类
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.11.06
 * @version 1.0
 */
class Load
{

    /**
     * 缓存路径
     *
     * @var string
     */
    protected $strCachePath;

    /**
     * 载入命名空间
     *
     * @var array
     */
    protected $arrNamespace = [ ];

    /**
     * 已经载入数据
     *
     * @var array
     */
    protected $arrLoad;

    /**
     * 构造函数
     *
     * @param array $arrNamespace
     * @return void
     */
    public function __construct(array $arrNamespace = [])
    {
        $this->arrNamespace = $arrNamespace;
    }

    /**
     * 设置缓存路径
     *
     * @param string $strCachePath
     * @return $this
     */
    public function setCachePath($strCachePath)
    {
        $this->strCachePath = $strCachePath;
        return $this;
    }

    /**
     * 添加命名空间
     *
     * @param array $arrNamespace
     * @return $this
     */
    public function addNamespace(array $arrNamespace)
    {
        $this->arrNamespace = array_merge($this->arrNamespace, $arrNamespace);
        return $this;
    }

    /**
     * 设置载入数据
     *
     * @param array $arrLoad
     * @return $this
     */
    public function setData(array $arrLoad)
    {
        $this->arrLoad = $arrLoad;
        return $this;
    }

    /**
     * 载入命令行数据
     *
     * @return array
     */
    public function loadData()
    {
        if (! is_null($this->arrLoad)) {
            return $this->arrLoad;
        }

        $arrFiles = $this->findConsoleFile($this->arrNamespace);

        $sCacheFile = $this->strCachePath;
        $sDir = dirname($sCacheFile);
        if (! is_dir($sDir)) {
            mkdir($sDir, 0777, true);
        }

        if (! file_put_contents($sCacheFile, '<?' . 'php /* ' . date('Y-m-d H:i:s') . ' */ ?' . '>' . PHP_EOL . '<?' . 'php return ' . var_export($arrFiles, true) . '; ?' . '>')) {
            throw new RuntimeException(sprintf('Dir %s do not have permission.', $sDir));
        }

        chmod($sCacheFile, 0777);

        return $this->arrLoad = $arrFiles;
    }

    /**
     * 分析目录中的 PHP 命令包包含的文件
     *
     * @param array $arrNamespace
     * @return array
     */
    public function findConsoleFile(array $arrNamespace)
    {
        $arrFiles = [ ];
        foreach ($arrNamespace as $sNamespace => $sDir) {
            if (! is_dir($sDir)) {
                continue;
            }

            $arrFiles = array_merge($arrFiles, array_map(function ($strItem) use ($sNamespace) {
                return $sNamespace . '\\' . basename($strItem, '.php');
            }, Fso::lists($sDir, 'file', false, [], [
                    'php'
            ])));
        }

        return $arrFiles;
    }
}
